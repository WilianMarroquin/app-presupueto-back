<?php

namespace App\Traits;

use App\Models\Budget;
use App\Models\BudgetTemplate;
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
        $today = today();
        $user = auth()->user();

        $activePeriod = $user->latestActiveBudgetTemplate;

        if (!$activePeriod || !$activePeriod->start_date) {
            return 0.0;
        }

        $startDate = Carbon::parse($activePeriod->start_date);

        // --- LÓGICA DE TIMEBOXING (Enmarcado de Tiempo) ---
        // Determinamos el ciclo real de evaluación basado en la frecuencia de la plantilla
        // Asumimos que puedes acceder a la relación period->name ('Mensual', 'Anual', etc.)
        $frecuencia = $activePeriod->budgetTemplate->period->name ?? 'Mensual';

        if ($frecuencia === 'Anual') {
            $rangoInicio = $today->copy()->startOfYear();
            $rangoFin = $activePeriod->end_date ? Carbon::parse($activePeriod->end_date) : $today->copy()->endOfYear();
        } else {
            // Por defecto: Evaluación Mensual
            $rangoInicio = $today->copy()->startOfMonth();
            $rangoFin = $activePeriod->end_date ? Carbon::parse($activePeriod->end_date) : $today->copy()->endOfMonth();
        }

        // Edge Case: Si el periodo se activó estrictamente DESPUÉS del inicio normal del ciclo
        // (Ej. Un presupuesto nuevo que arrancó el 15 de mayo y no retroactivamente)
        if ($startDate->gt($rangoInicio)) {
            $rangoInicio = $startDate->copy();
        }

        // Sanity Check: ¿Estamos fuera de la caja de tiempo?
        if ($today->gt($rangoFin) || $today->lt($rangoInicio)) {
            return 0.0;
        }

        $categoriasFijas = [TransactionCategory::PAGOS_TC];

        // OJO: Recuperé tu filtro de 'Expense' (Gastos Puros).
        // Si no lo pones, el sistema restará cosas que no debe de tu dinero de bolsillo.
        $budgetItems = $activePeriod->budgetTemplate->items()
            ->whereHas('category', function ($query) {
                // Ajusta "Expense" al valor exacto de tu Enum o String en la DB
                $query->where('type', 'Expense');
            })
            ->whereNotIn('transaction_category_id', $categoriasFijas)
            ->get();

        if ($budgetItems->isEmpty()) {
            return 0.0;
        }

        $categoryIds = $budgetItems->pluck('transaction_category_id');

        // Calculamos gastos SOLO en nuestra "caja de tiempo" actual
        $expenses = Transaction::whereBetween('transaction_date', [$rangoInicio, $rangoFin])
            ->whereIn('category_id', $categoryIds)
            ->select('category_id', DB::raw('sum(amount) as total_spent'))
            ->groupBy('category_id')
            ->pluck('total_spent', 'category_id');

        $totalRemaining = 0;

        foreach ($budgetItems as $item) {
            $spent = $expenses[$item->transaction_category_id] ?? 0;
            $totalRemaining += max(0, $item->category_limit - $spent);
        }

        // Factor Tiempo: Dividimos solo entre los días que quedan del ciclo actual
        $daysRemaining = $today->diffInDays($rangoFin) + 1;

        return ($daysRemaining > 0) ? ($totalRemaining / $daysRemaining) : 0.0;
    }

    public function getDaylyCoatch(): array
    {
//        $context = $this->getDailyContext();

//        $aiService = new AIService();

//        $respuestaIa = $aiService->getDailyCoach($context);

        return [
            'estado_alerta' => 0,
            'total_gastado_hoy' => 0,
//            'respuesta_ai' => $respuestaIa,
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
            ->where('id', '!=', TransactionCategory::PAGOS_TC);
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
        $presupuestoTotalVariable = BudgetTemplate::where('start_date', '<=', $now)
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
