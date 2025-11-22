<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\AppBaseController;
use App\Models\Transaction;
use App\Models\TransactionCategory;
use Illuminate\Http\Request;

class AnalyticsApiController extends AppBaseController
{
    public function getTotalExpensesByCategory(Request $request)
    {
        $transactions = Transaction::query();

        if ($request->filled('start_date')) {
            $transactions->whereDate('transaction_date', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $transactions->whereDate('transaction_date', '<=', $request->input('end_date'));
        }

        $data = $transactions->whereHas('category.parent') // Solo transacciones con subcategoría (que tiene padre)
            ->whereHas('category', function($query) {
                $query->where('type', TransactionCategory::CATEGORY_TYPE_EXPENSE);
            })
            ->get();

        // Agrupar por categoría padre y sumar montos
        $totals = $data
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

        return $this->sendResponse($totals, 'Total expenses by category retrieved successfully.');
    }
}
