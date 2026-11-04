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

        $currentPeriodStartDate = $request->input('start_date');
        $currentPeriodEndDate = $request->input('end_date');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $transactions->whereBetween('budget_date', [$currentPeriodStartDate, $currentPeriodEndDate]);
        } else {
            if ($request->filled('start_date')) {
                $transactions->whereDate('budget_date', '>=', $currentPeriodStartDate);
            }

            if ($request->filled('end_date')) {
                $transactions->whereDate('budget_date', '<=', $currentPeriodEndDate);
            }
        }

        $data = $transactions->whereHas('category', function($query) {
                $query->where('type', TransactionCategory::CATEGORY_TYPE_EXPENSE);
            })
            ->get();

        $totals = $data
            ->groupBy('category_id')
            ->map(function ($group) {
                return [
                    'category_id' => $group->first()->category_id,
                    'category_name' => $group->first()->category->name,
                    'total_amount' => $group->sum('amount'),
                ];
            })
            ->values();

        return $this->sendResponse($totals, 'Total expenses by category retrieved successfully.');
    }
}
