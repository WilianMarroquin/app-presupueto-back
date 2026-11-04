<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeOverviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $recentTransactions = collect($this->resource['recent_transactions'] ?? [])->map(function ($tx) {
            $date = $tx->transaction_date ? Carbon::parse($tx->transaction_date) : Carbon::now();

            if ($date->isToday()) {
                $relativeDate = 'Hoy';
            } elseif ($date->isYesterday()) {
                $relativeDate = 'Ayer';
            } else {
                $relativeDate = ucfirst($date->locale('es')->translatedFormat('d M'));
            }

            return [
                'id'               => $tx->id,
                'description'      => $tx->description ?: ($tx->category->name ?? 'Transacción'),
                'amount'           => round((float) $tx->amount, 2),
                'transaction_date' => $date->toDateString(),
                'relative_date'    => $relativeDate,
                'type'             => $tx->category->type ?? 'Expense',
                'icon'             => $tx->category->icon ?: 'ri-exchange-dollar-line',
                'color'            => $tx->category->color ?: 'primary',
                'category'         => $tx->category ? [
                    'id'    => $tx->category->id,
                    'name'  => $tx->category->name,
                    'type'  => $tx->category->type,
                    'icon'  => $tx->category->icon,
                    'color' => $tx->category->color,
                ] : null,
                'account'          => $tx->account ? [
                    'id'   => $tx->account->id,
                    'name' => $tx->account->name,
                ] : null,
            ];
        })->values();

        return [
            'total_accounts_balance' => round((float) ($this->resource['total_accounts_balance'] ?? 0), 2),
            'budget'                 => [
                'remaining'       => round((float) ($this->resource['budget']['remaining'] ?? 0), 2),
                'total_budgeted'  => round((float) ($this->resource['budget']['total_budgeted'] ?? 0), 2),
                'total_spent'     => round((float) ($this->resource['budget']['total_spent'] ?? 0), 2),
                'percentage_used' => round((float) ($this->resource['budget']['percentage_used'] ?? 0), 1),
                'days_remaining'  => (int) ($this->resource['budget']['days_remaining'] ?? 0),
                'period_name'     => (string) ($this->resource['budget']['period_name'] ?? 'Sin período activo'),
                'status'          => (string) ($this->resource['budget']['status'] ?? 'success'),
            ],
            'alerts'                 => [
                'pending_fixed_expenses_count'  => (int) ($this->resource['alerts']['pending_fixed_expenses_count'] ?? 0),
                'pending_fixed_expenses_amount' => round((float) ($this->resource['alerts']['pending_fixed_expenses_amount'] ?? 0), 2),
            ],
            'recent_transactions'    => $recentTransactions,
            'quick_actions'          => $this->resource['quick_actions'] ?? [],
            'top_categories'         => collect($this->resource['top_categories'] ?? [])->values(),
            'ai_daily_insight'       => (string) ($this->resource['ai_daily_insight'] ?? ''),
        ];
    }
}
