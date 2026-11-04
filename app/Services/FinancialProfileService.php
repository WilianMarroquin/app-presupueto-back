<?php

namespace App\Services;

use App\Models\Account;
use App\Models\AccountType;
use App\Models\BudgetItem;
use App\Models\BudgetPeriod;
use App\Models\FixedExpense;
use App\Models\InstallmentPlan;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinancialProfileService
{
    /**
     * Consolida el perfil financiero completo del usuario en tiempo real.
     */
    public function buildProfileSnapshot(): array
    {
        $now = Carbon::now();

        return [
            'generated_at'           => $now->format('Y-m-d H:i:s'),
            'currency'               => 'GTQ (Q)',
            'liquidity'              => $this->getLiquiditySnapshot(),
            'credit_cards'           => $this->getCreditCardsSnapshot(),
            'installment_plans'      => $this->getInstallmentPlansSnapshot(),
            'active_budget_period'   => $this->getActivePeriodSnapshot($now),
            'previous_budget_period' => $this->getPreviousPeriodSnapshot(),
            'pending_fixed_expenses' => $this->getPendingFixedExpensesSnapshot(),
            'recent_habits'          => $this->getRecentHabitsSnapshot($now),
        ];
    }

    /**
     * 1. Liquidez total: Cuentas bancarias y efectivo activas (nature = 'asset', withoutCreditCard).
     */
    protected function getLiquiditySnapshot(): array
    {
        $accounts = Account::query()
            ->with('type:id,name')
            ->where('nature', 'asset')
            ->withoutCreditCard()
            ->where('is_active', true)
            ->orderByDesc('current_balance')
            ->get();

        $totalBalance = (float) $accounts->sum('current_balance');

        return [
            'total_balance' => round($totalBalance, 2),
            'accounts_count' => $accounts->count(),
            'accounts'      => $accounts->map(fn (Account $account) => [
                'id'               => $account->id,
                'name'             => $account->name,
                'type'             => $account->type->name ?? 'Cuenta',
                'is_transactional' => (bool) $account->is_transactional,
                'balance'          => round((float) $account->current_balance, 2),
            ])->values()->all(),
        ];
    }

    /**
     * 2. Tarjetas de crédito: saldo consumido, límite, día de corte y día de pago.
     */
    protected function getCreditCardsSnapshot(): array
    {
        $cards = Account::query()
            ->with('creditCardDetail')
            ->where('type_id', AccountType::CREDIT_CARD)
            ->where('is_active', true)
            ->get();

        $totalDebt = 0.0;

        $cardsList = $cards->map(function (Account $card) use (&$totalDebt) {
            $detail = $card->creditCardDetail;

            $pendingTransactionsSum = (float) Transaction::query()
                ->where('account_id', $card->id)
                ->where('is_settled', 0)
                ->whereHas('category', fn ($q) => $q->where('type', TransactionCategory::CATEGORY_TYPE_EXPENSE))
                ->sum('amount');

            $currentConsumed = max((float) $card->current_balance, $pendingTransactionsSum);
            $creditLimit = $detail && $detail->credit_limit ? (float) $detail->credit_limit : null;
            $availableCredit = $creditLimit !== null ? max(0, $creditLimit - $currentConsumed) : null;

            $totalDebt += $currentConsumed;

            return [
                'id'                      => $card->id,
                'name'                    => $card->name,
                'alias'                   => $detail->alias ?? null,
                'network'                 => $detail->network ?? null,
                'last_4'                  => $detail->last_4 ?? null,
                'current_balance_consumed' => round($currentConsumed, 2),
                'unsettled_expenses_sum'  => round($pendingTransactionsSum, 2),
                'credit_limit'            => $creditLimit !== null ? round($creditLimit, 2) : null,
                'available_credit'        => $availableCredit !== null ? round($availableCredit, 2) : null,
                'cutoff_day'              => $detail->cutoff_day ?? null,
                'payment_day'             => $detail->payment_day ?? null,
            ];
        })->values()->all();

        return [
            'total_credit_card_debt' => round($totalDebt, 2),
            'cards_count'            => count($cardsList),
            'cards'                  => $cardsList,
        ];
    }

    /**
     * 3. Visacuotas y Planes en cuotas (Installment Plans) activos.
     */
    protected function getInstallmentPlansSnapshot(): array
    {
        $plans = InstallmentPlan::query()
            ->with(['creditCard:id,name', 'payments'])
            ->where('status', InstallmentPlan::STATUS_ACTIVE)
            ->orderByDesc('id')
            ->get();

        $totalMonthlyCommitment = 0.0;
        $totalRemainingBalance = 0.0;

        $plansList = $plans->map(function (InstallmentPlan $plan) use (&$totalMonthlyCommitment, &$totalRemainingBalance) {
            $payments = $plan->payments ?? collect();

            $paidInstallmentsCount = $payments->where('is_settled', 1)->count();
            $pendingGeneratedCount = $payments->where('is_settled', 0)->count();

            $totalInstallments = (int) $plan->total_installments;
            $pendingInstallmentsCount = max(0, $totalInstallments - $paidInstallmentsCount);

            $paidAmount = (float) $payments->where('is_settled', 1)->sum('amount');
            if ($paidAmount <= 0 && $paidInstallmentsCount > 0) {
                $paidAmount = $paidInstallmentsCount * $plan->monthly_fee;
            }

            $remainingBalance = max(0, (float) $plan->total_amount - $paidAmount);
            $monthlyFee = (float) $plan->monthly_fee;

            if ($pendingInstallmentsCount > 0) {
                $totalMonthlyCommitment += $monthlyFee;
                $totalRemainingBalance += $remainingBalance;
            }

            return [
                'id'                            => $plan->id,
                'description'                   => $plan->name,
                'credit_card'                   => $plan->creditCard->name ?? 'N/A',
                'total_amount'                  => round((float) $plan->total_amount, 2),
                'monthly_installment_amount'    => round($monthlyFee, 2),
                'total_installments'            => $totalInstallments,
                'paid_installments'             => $paidInstallmentsCount,
                'pending_installments'          => $pendingInstallmentsCount,
                'unsettled_billed_installments' => $pendingGeneratedCount,
                'remaining_balance_to_pay'      => round($remainingBalance, 2),
                'start_date'                    => $plan->start_date?->format('Y-m-d'),
            ];
        })->values()->all();

        return [
            'active_plans_count'       => count($plansList),
            'total_monthly_commitment' => round($totalMonthlyCommitment, 2),
            'total_remaining_balance'  => round($totalRemainingBalance, 2),
            'plans'                    => $plansList,
        ];
    }

    /**
     * 4. Período Contable Activo usando scope onlyCurrentAccountingPeriod().
     */
    protected function getActivePeriodSnapshot(Carbon $now): ?array
    {
        /** @var BudgetPeriod|null $activePeriod */
        $activePeriod = BudgetPeriod::onlyCurrentAccountingPeriod()
            ->with('budgetTemplate:id,name')
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->first();

        if (!$activePeriod) {
            return null;
        }

        $startDate = $activePeriod->start_date->copy()->startOfDay();
        $endDate = ($activePeriod->end_date
            ? $activePeriod->end_date->copy()
            : max($activePeriod->start_date->copy()->endOfMonth(), $now->copy()->endOfMonth())
        )->endOfDay();

        $daysRemaining = max(0, (int) $now->copy()->startOfDay()->diffInDays($endDate->copy()->startOfDay(), false) + 1);

        // Límites por categoría de la plantilla activa
        $budgetItems = BudgetItem::with('category:id,name,type')
            ->where('budget_template_id', $activePeriod->budget_template_id)
            ->whereHas('category', fn ($q) => $q->where('type', TransactionCategory::CATEGORY_TYPE_EXPENSE))
            ->get();

        $totalBudgeted = (float) $budgetItems->sum('category_limit');
        if ($totalBudgeted <= 0 && $activePeriod->total_budgeted > 0) {
            $totalBudgeted = (float) $activePeriod->total_budgeted;
        }

        // Gastos reales del período evaluando COALESCE(budget_date, transaction_date)
        $expensesByCategory = Transaction::query()
            ->select('category_id', DB::raw('SUM(amount) as total_spent'))
            ->whereRaw('DATE(COALESCE(budget_date, transaction_date)) BETWEEN ? AND ?', [
                $startDate->toDateString(),
                $endDate->toDateString(),
            ])
            ->whereHas('category', fn ($q) => $q->where('type', TransactionCategory::CATEGORY_TYPE_EXPENSE))
            ->groupBy('category_id')
            ->with('category:id,name')
            ->get()
            ->keyBy('category_id');

        $totalSpent = (float) $expensesByCategory->sum('total_spent');
        $remainingBudget = $totalBudgeted - $totalSpent;
        $percentageUsed = $totalBudgeted > 0 ? round(($totalSpent / $totalBudgeted) * 100, 2) : 0.0;

        $categoryBreakdown = [];
        $processedCategoryIds = [];

        foreach ($budgetItems as $item) {
            $catId = $item->transaction_category_id;
            $processedCategoryIds[] = $catId;

            $limit = (float) $item->category_limit;
            $spent = isset($expensesByCategory[$catId]) ? (float) $expensesByCategory[$catId]->total_spent : 0.0;

            $categoryBreakdown[] = [
                'category_id'     => $catId,
                'category_name'   => $item->category->name ?? 'Categoría',
                'budgeted_limit'  => round($limit, 2),
                'actual_spent'    => round($spent, 2),
                'remaining'       => round($limit - $spent, 2),
                'percentage_used' => $limit > 0 ? round(($spent / $limit) * 100, 1) : 100.0,
                'is_over_budget'  => $spent > $limit,
            ];
        }

        // Agregar categorías con gasto que no estaban en la plantilla (gastos huérfanos)
        foreach ($expensesByCategory as $catId => $expenseRow) {
            if (!in_array($catId, $processedCategoryIds, true)) {
                $spent = (float) $expenseRow->total_spent;
                $categoryBreakdown[] = [
                    'category_id'     => $catId,
                    'category_name'   => ($expenseRow->category->name ?? 'Categoría fuera de plantilla') . ' (No presupuestada)',
                    'budgeted_limit'  => 0.0,
                    'actual_spent'    => round($spent, 2),
                    'remaining'       => round(-$spent, 2),
                    'percentage_used' => 100.0,
                    'is_over_budget'  => true,
                ];
            }
        }

        return [
            'period_id'          => $activePeriod->id,
            'template_name'      => $activePeriod->budgetTemplate->name ?? 'Plantilla Activa',
            'start_date'         => $startDate->toDateString(),
            'end_date'           => $endDate->toDateString(),
            'days_remaining'     => $daysRemaining,
            'total_budgeted'     => round($totalBudgeted, 2),
            'total_spent'        => round($totalSpent, 2),
            'remaining_budget'   => round($remainingBudget, 2),
            'percentage_used'    => $percentageUsed,
            'safe_daily_spend'   => $daysRemaining > 0 && $remainingBudget > 0
                ? round($remainingBudget / $daysRemaining, 2)
                : 0.0,
            'category_breakdown' => $categoryBreakdown,
        ];
    }

    /**
     * 5. Período Contable Anterior (end_date no nulo, ordenado por end_date desc).
     */
    protected function getPreviousPeriodSnapshot(): ?array
    {
        /** @var BudgetPeriod|null $previousPeriod */
        $previousPeriod = BudgetPeriod::query()
            ->with('budgetTemplate:id,name')
            ->whereNotNull('end_date')
            ->orderByDesc('end_date')
            ->orderByDesc('id')
            ->first();

        if (!$previousPeriod) {
            return null;
        }

        $startDate = $previousPeriod->start_date->toDateString();
        $endDate = $previousPeriod->end_date->toDateString();

        $totalBudgeted = (float) BudgetItem::where('budget_template_id', $previousPeriod->budget_template_id)
            ->whereHas('category', fn ($q) => $q->where('type', TransactionCategory::CATEGORY_TYPE_EXPENSE))
            ->sum('category_limit');

        if ($totalBudgeted <= 0 && $previousPeriod->total_budgeted > 0) {
            $totalBudgeted = (float) $previousPeriod->total_budgeted;
        }

        $expensesByCategory = Transaction::query()
            ->select('category_id', DB::raw('SUM(amount) as total_spent'))
            ->whereRaw('DATE(COALESCE(budget_date, transaction_date)) BETWEEN ? AND ?', [$startDate, $endDate])
            ->whereHas('category', fn ($q) => $q->where('type', TransactionCategory::CATEGORY_TYPE_EXPENSE))
            ->groupBy('category_id')
            ->orderByDesc('total_spent')
            ->with('category:id,name')
            ->get();

        $totalSpent = (float) $expensesByCategory->sum('total_spent');

        return [
            'period_id'      => $previousPeriod->id,
            'template_name'  => $previousPeriod->budgetTemplate->name ?? 'Período Anterior',
            'start_date'     => $startDate,
            'end_date'       => $endDate,
            'total_budgeted' => round($totalBudgeted, 2),
            'total_spent'    => round($totalSpent, 2),
            'difference'     => round($totalBudgeted - $totalSpent, 2),
            'top_categories' => $expensesByCategory->take(5)->map(fn ($row) => [
                'category_name' => $row->category->name ?? 'Categoría',
                'total_spent'   => round((float) $row->total_spent, 2),
            ])->values()->all(),
        ];
    }

    /**
     * 6. Gastos fijos pendientes en el período contable activo.
     */
    protected function getPendingFixedExpensesSnapshot(): array
    {
        $activePeriod = BudgetPeriod::onlyCurrentAccountingPeriod()
            ->orderByDesc('is_active')
            ->orderByDesc('id')
            ->first();

        $query = FixedExpense::query()
            ->with('category:id,name')
            ->where('is_active', 1);

        if ($activePeriod) {
            $query->whereDoesntHave('paidPeriods', function ($q) use ($activePeriod) {
                $q->where('budget_period_id', $activePeriod->id);
            });
        }

        $pendingFixed = $query->orderBy('due_day')->get();
        $totalPendingAmount = (float) $pendingFixed->sum('base_amount');

        return [
            'pending_count'        => $pendingFixed->count(),
            'total_pending_amount' => round($totalPendingAmount, 2),
            'items'                => $pendingFixed->map(fn (FixedExpense $item) => [
                'id'          => $item->id,
                'name'        => $item->name,
                'category'    => $item->category->name ?? 'General',
                'base_amount' => round((float) $item->base_amount, 2),
                'due_day'     => $item->due_day,
                'is_variable' => (bool) $item->is_variable,
            ])->values()->all(),
        ];
    }

    /**
     * 7. Hábitos recientes: Top 3 categorías de mayor consumo del mes en curso y promedio de gasto diario.
     */
    protected function getRecentHabitsSnapshot(Carbon $now): array
    {
        $startOfMonth = $now->copy()->startOfMonth()->toDateString();
        $today = $now->toDateString();
        $daysElapsed = max(1, $now->day);

        $monthlyCategoryTotals = Transaction::query()
            ->select('category_id', DB::raw('SUM(amount) as total_spent'), DB::raw('COUNT(*) as transactions_count'))
            ->whereRaw('DATE(COALESCE(budget_date, transaction_date)) BETWEEN ? AND ?', [$startOfMonth, $today])
            ->whereHas('category', fn ($q) => $q->where('type', TransactionCategory::CATEGORY_TYPE_EXPENSE))
            ->groupBy('category_id')
            ->orderByDesc('total_spent')
            ->with('category:id,name')
            ->get();

        $totalSpentMonthToDate = (float) $monthlyCategoryTotals->sum('total_spent');
        $dailyAverageSpend = round($totalSpentMonthToDate / $daysElapsed, 2);

        $top3Categories = $monthlyCategoryTotals->take(3)->map(fn ($row) => [
            'category_name'      => $row->category->name ?? 'Categoría',
            'total_spent'        => round((float) $row->total_spent, 2),
            'transactions_count' => (int) $row->transactions_count,
            'share_percentage'   => $totalSpentMonthToDate > 0
                ? round(((float) $row->total_spent / $totalSpentMonthToDate) * 100, 1)
                : 0.0,
        ])->values()->all();

        return [
            'month'                   => $now->translatedFormat('F Y'),
            'days_elapsed_in_month'   => $daysElapsed,
            'total_spent_month_to_date' => round($totalSpentMonthToDate, 2),
            'daily_average_spend'     => $dailyAverageSpend,
            'top_3_categories'        => $top3Categories,
        ];
    }
}
