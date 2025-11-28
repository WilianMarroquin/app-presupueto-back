<?php

namespace App\Traits;

use App\Models\Budget;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Services\AIService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

trait HomeTrait
{


    public function getDisponibleHoy(): float
    {
        $today = now();

        // 1. Rango del Mes
        $startOfMonth = $today->copy()->startOfMonth();
        $endOfMonth = $today->copy()->endOfMonth();

        // 2. Definir qué NO es "Dinero de Bolsillo"
        // ID 12 = Finanzas (Deudas, TC, Créditos)
        // Tip: También podrías agregar aquí el ID de Vivienda (Renta) si es fijo.
        $categoriasExcluidas = [TransactionCategory::FINANZAS];

        // 3. Traer SOLO Presupuestos Variables
        $budgets = Budget::where('start_date', '<=', $today)
            ->where('end_date', '>=', $today)
            ->whereNotIn('category_id', $categoriasExcluidas) // <--- EL FILTRO MÁGICO
            ->get();

        // 4. Calcular lo Gastado (Solo de esas categorías)
        // Obtenemos los IDs que sí nos interesan para filtrar las transacciones también
        $categoryIds = $budgets->pluck('category_id');

        $expenses = Transaction::whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            ->whereIn('category_id', $categoryIds) // Optimización: Solo sumar gastos relevantes
            ->select('category_id', DB::raw('sum(amount) as total_spent'))
            ->groupBy('category_id')
            ->pluck('total_spent', 'category_id');

        $totalRemaining = 0;

        foreach ($budgets as $budget) {
            $spent = $expenses[$budget->category_id] ?? 0;

            // Sumamos lo que sobra
            // Usamos max(0) para que si te pasaste en Comida, no reste al dinero de Gasolina
            $totalRemaining += max(0, $budget->amount - $spent);
        }

        // 5. Factor Tiempo
        $daysRemaining = $today->diffInDays($endOfMonth) + 1;

        // 6. Resultado Final
        // Esto es "Cuánto puedo gastar hoy en cosas variables sin endeudarme"
        return ($daysRemaining > 0) ? ($totalRemaining / $daysRemaining) : 0;
    }

    public function getDaylyCoatch(): array
    {
        $context = $this->getDailyContext();

        $aiService = new AIService();

        $respuestaIa = $aiService->getDailyCoach($context);

        return [
            'estado_alerta' => $context['estado_alerta'],
            'total_gastado_hoy' => $context['total_gastado_hoy'],
            'respuesta_ai' => $respuestaIa,
        ];
    }

    public function getDailyContext(): array
    {
        // 1. PREPARAR EL TERRENO (Fechas Precisas)
        $now = now();
        $todayStart = $now->copy()->startOfDay();
        $todayEnd   = $now->copy()->endOfDay();
        $yesterdayStart = $now->copy()->subDay()->startOfDay();
        $yesterdayEnd   = $now->copy()->subDay()->endOfDay();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd   = $now->copy()->endOfMonth();

        // ID de la categoría a excluir (Finanzas/Deudas)
        $excludedCatId = 12;

        // 2. DEFINIR LA CONSULTA BASE (Para no repetir código)
        // Esto no ejecuta la query todavía, solo prepara el "molde"
        $baseExpenseQuery = Transaction::whereHas('category', function ($q) {
            $q->where('type', 'expense')
            ->where('id', '!=', TransactionCategory::FINANZAS);
        });

        // 3. DATOS DE HOY (El Nuevo Requerimiento)
        // Clonamos la query base para no ensuciarla
        $gastoHoy = (clone $baseExpenseQuery)
            ->whereBetween('transaction_date', [$todayStart, $todayEnd])
            ->sum('amount');

        // 4. DATOS DE AYER (Total y Culpable en paralelo)
        // Optimizacion: Traemos todas las transacciones de ayer con su categoría en una sola colección
        // y hacemos los cálculos en memoria (PHP) en lugar de hacer 2 viajes a la DB.
        // Esto es más rápido cuando no son miles de transacciones por día.
        $transaccionesAyer = (clone $baseExpenseQuery)
            ->whereBetween('transaction_date', [$yesterdayStart, $yesterdayEnd])
            ->with('category:id,name') // Solo traemos id y nombre para ahorrar memoria
            ->get();

        $gastoAyer = $transaccionesAyer->sum('amount');

        // Encontrar al culpable usando colecciones de Laravel (CPU local vs DB CPU)
        $topCategoryAyer = $transaccionesAyer
            ->where('category_id', '!=', $excludedCatId)
            ->groupBy('category_id')
            ->map(fn ($row) => $row->sum('amount'))
            ->sortDesc()
            ->keys()
            ->first(); // Nos da el ID

        // Buscamos el nombre en la colección cargada (sin ir a la DB de nuevo)
        $nombreCulpable = 'Nada';
        if ($topCategoryAyer) {
            $cat = $transaccionesAyer->firstWhere('category_id', $topCategoryAyer)->category;
            $nombreCulpable = $cat ? $cat->name : 'Desconocido';
        }

        // 5. CONTEXTO GENERAL (Panorama del Mes)
        // Presupuesto Variable Total
        $presupuestoTotalVariable = Budget::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('category_id', '!=', $excludedCatId)
            ->sum('amount');

        // Gasto Variable Total del Mes
        $gastoTotalVariable = (clone $baseExpenseQuery)
            ->whereBetween('transaction_date', [$monthStart, $monthEnd])
            ->where('category_id', '!=', $excludedCatId)
            ->sum('amount');

        $remanenteMes = max(0, $presupuestoTotalVariable - $gastoTotalVariable);
        $diasRestantes = $now->diffInDays($monthEnd) + 1; // +1 para incluir hoy

        // Obtenemos la meta diaria (asumiendo que tu método ya está optimizado)
        $disponibleDiarioIdeal = $this->getDisponibleHoy();

        // 6. RETORNO DEL PAQUETE
        return [
            'fecha' => $yesterdayStart->format('d/m/Y'),
            'gasto_ayer' => (float) $gastoAyer,
            'total_gastado_hoy' => (float) $gastoHoy, // <--- NUEVO CAMPO
            'categoria_top' => $nombreCulpable,
            'presupuesto_restante' => (float) $remanenteMes,
            'dias_restantes' => $diasRestantes,
            'meta_diaria_segura' => (float) $disponibleDiarioIdeal,
            'estado_alerta' => ($gastoAyer > ($disponibleDiarioIdeal * 2)),
        ];
    }
}
