<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Http\Resources\HomeOverviewResource;
use App\Models\Account;
use App\Models\BudgetItem;
use App\Models\BudgetPeriod;
use App\Models\FixedExpense;
use App\Models\QuickAction;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Traits\HomeTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeApiController extends AppBaseController
{
    use HomeTrait;

    /**
     * Endpoint agregador del Home Financiero (Centro de Mando).
     * GET /api/v1/home/overview
     */
    public function overview(Request $request): JsonResponse
    {
        // 1. Liquidez Total: Suma directa en BD (bancos y efectivo activos sin TC)
        $totalAccountsBalance = (float) Account::query()
            ->where('is_active', true)
            ->where('nature', 'asset')
            ->withoutCreditCard()
            ->sum('current_balance');

        // 2. Período Contable Activo
        $activePeriod = BudgetPeriod::with('budgetTemplate:id,name')
            ->onlyCurrentAccountingPeriod()
            ->first();

        $totalBudgeted = 0.0;
        $totalSpent = 0.0;
        $remaining = 0.0;
        $percentageUsed = 0.0;
        $daysRemaining = 0;
        $periodName = 'Sin período activo';
        $budgetStatus = 'success';
        $topCategories = collect();

        if ($activePeriod) {
            $periodStart = $activePeriod->start_date ? Carbon::parse($activePeriod->start_date) : now()->startOfMonth();
            $periodEnd = $activePeriod->end_date ? Carbon::parse($activePeriod->end_date) : $periodStart->copy()->endOfMonth();

            $periodName = sprintf(
                '%s - %s',
                ucfirst(str_replace('.', '', $periodStart->locale('es')->translatedFormat('M d'))),
                ucfirst(str_replace('.', '', $periodEnd->locale('es')->translatedFormat('M d')))
            );

            $daysRemaining = max(0, (int) today()->diffInDays($periodEnd, false));

            // Obtener límites presupuestados por categoría de gasto en una sola pasada
            $budgetItems = BudgetItem::query()
                ->where('budget_template_id', $activePeriod->budget_template_id)
                ->whereHas('category', fn ($q) => $q->where('type', TransactionCategory::CATEGORY_TYPE_EXPENSE))
                ->with('category:id,name,icon,color')
                ->get(['id', 'budget_template_id', 'transaction_category_id', 'category_limit']);

            $totalBudgeted = (float) $budgetItems->sum('category_limit');
            if ($totalBudgeted <= 0 && $activePeriod->total_budgeted > 0) {
                $totalBudgeted = (float) $activePeriod->total_budgeted;
            }

            // Agregación de transacciones optimizada (usando COALESCE para evitar OR lento con índices)
            $startDateStr = $periodStart->toDateString();
            $endDateStr = $periodEnd->toDateString();

            $expensesByCategory = Transaction::query()
                ->whereHas('category', fn ($q) => $q->where('type', TransactionCategory::CATEGORY_TYPE_EXPENSE))
                ->whereRaw('COALESCE(budget_date, transaction_date) BETWEEN ? AND ?', [$startDateStr, $endDateStr])
                ->whereIn('category_id', $budgetItems->pluck('transaction_category_id'))
                ->groupBy('category_id')
                ->pluck(DB::raw('SUM(amount)'), 'category_id');

            $totalSpent = (float) $expensesByCategory->sum();
            $remaining = $totalBudgeted - $totalSpent;

            $percentageUsed = $totalBudgeted > 0
                ? round(($totalSpent / $totalBudgeted) * 100, 1)
                : ($totalSpent > 0 ? 100.0 : 0.0);

            if ($percentageUsed >= 90 || ($totalBudgeted > 0 && $totalSpent > $totalBudgeted)) {
                $budgetStatus = 'error';
            } elseif ($percentageUsed >= 75) {
                $budgetStatus = 'warning';
            }

            // Top 3 Categorías consumidas
            $topCategories = $budgetItems
                ->map(function ($item) use ($expensesByCategory) {
                    $limit = (float) $item->category_limit;
                    $spent = (float) ($expensesByCategory[$item->transaction_category_id] ?? 0);
                    $pct = $limit > 0 ? round(($spent / $limit) * 100, 1) : ($spent > 0 ? 100.0 : 0.0);

                    return [
                        'id'              => $item->transaction_category_id,
                        'name'            => $item->category->name ?? 'Categoría',
                        'icon'            => $item->category->icon ?: 'ri-price-tag-3-line',
                        'color'           => $item->category->color ?: 'primary',
                        'spent'           => round($spent, 2),
                        'limit'           => round($limit, 2),
                        'percentage_used' => $pct,
                    ];
                })
                ->sortByDesc(fn ($cat) => [$cat['percentage_used'], $cat['spent']])
                ->take(3)
                ->values();
        }

        // 3. Próximos Vencimientos: conteo y suma en UNA sola consulta SQL
        $pendingFixedQuery = FixedExpense::query()->where('is_active', true);

        if ($activePeriod) {
            $pendingFixedQuery->whereDoesntHave('paidPeriods', fn ($q) => $q->where('budget_period_id', $activePeriod->id));
        }

        $pendingStats = $pendingFixedQuery
            ->selectRaw('COUNT(*) as total_count, COALESCE(SUM(base_amount), 0) as total_amount')
            ->first();

        $pendingFixedExpensesCount = (int) ($pendingStats->total_count ?? 0);
        $pendingFixedExpensesAmount = (float) ($pendingStats->total_amount ?? 0.0);

        // 4. Transacciones Recientes (solo columnas estrictamente necesarias)
        $recentTransactions = Transaction::query()
            ->select(['id', 'description', 'amount', 'transaction_date', 'budget_date', 'category_id', 'account_id'])
            ->with([
                'category:id,name,type,icon,color',
                'account:id,name',
            ])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // 5. Acciones Frecuentes
        $quickActions = QuickAction::query()
            ->select(['id', 'name', 'category_id', 'account_id', 'payment_method_id', 'amount', 'description', 'icon', 'color'])
            ->with([
                'category:id,name,icon,color',
                'account:id,name',
                'paymentMethod:id,name',
            ])
            ->orderByDesc('id')
            ->limit(6)
            ->get();

        // 6. Insight Diario Precalculado
        if (!$activePeriod) {
            $aiDailyInsight = 'Aún no tienes un período activo. Configura una plantilla para empezar a monitorear tu liquidez.';
        } elseif ($remaining < 0) {
            $exceso = number_format(abs($remaining), 2, '.', ',');
            $aiDailyInsight = "Has superado tu presupuesto por Q{$exceso} ({$percentageUsed}% usado). Prioriza gastos esenciales.";
        } else {
            $formattedRemaining = number_format($remaining, 0, '.', ',');
            $pctRounded = round($percentageUsed);
            $aiDailyInsight = "Tienes Q{$formattedRemaining} disponibles para este ciclo. Vas gastando al {$pctRounded}% de tu límite.";
        }

        return $this->sendResponse(
            (new HomeOverviewResource([
                'total_accounts_balance' => $totalAccountsBalance,
                'budget'                 => [
                    'remaining'       => $remaining,
                    'total_budgeted'  => $totalBudgeted,
                    'total_spent'     => $totalSpent,
                    'percentage_used' => $percentageUsed,
                    'days_remaining'  => $daysRemaining,
                    'period_name'     => $periodName,
                    'status'          => $budgetStatus,
                ],
                'alerts'                 => [
                    'pending_fixed_expenses_count'  => $pendingFixedExpensesCount,
                    'pending_fixed_expenses_amount' => $pendingFixedExpensesAmount,
                ],
                'recent_transactions'    => $recentTransactions,
                'quick_actions'          => $quickActions,
                'top_categories'         => $topCategories,
                'ai_daily_insight'       => $aiDailyInsight,
            ]))->resolve($request),
            'Resumen general del centro de mando recuperado con éxito.'
        );
    }

    public function detailDaily(): JsonResponse
    {
        $disponibleHoy = $this->getDisponibleHoy();
        $mensajeDiario = $this->getDaylyCoatch();

        $data = [
            'disponible_hoy' => $disponibleHoy,
            'gastado_hoy'    => $mensajeDiario['total_gastado_hoy'],
            'estado_alerta'  => $mensajeDiario['estado_alerta'],
        ];

        return $this->sendResponse($data, 'Disponible hoy calculado correctamente.');
    }
}
