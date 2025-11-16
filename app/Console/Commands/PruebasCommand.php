<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use App\Models\TransactionCategory;
use App\Models\User;
use Illuminate\Console\Command;

class PruebasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pruebas:command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $transactions = Transaction::with(['category.parent'])
            ->whereHas('category.parent')
            ->whereHas('category', function($query) {
                $query->where('type', TransactionCategory::CATEGORY_TYPE_EXPENSE);
            })
            ->get();

        // Agrupar por categoría padre y sumar montos
        $totals = $transactions
            ->groupBy(function ($transaction) {
                return optional($transaction->category->parent)->id;
            })
            ->map(function ($group) {
                $parent = $group->first()->category->parent ?? null;
                return [
                    'category_id' => optional($parent)->id,
                    'category_name' => optional($parent)->name,
                    'total_amount' => $group->sum('amount'),
                ];
            })
            ->values();

        dd($totals);
    }
}
