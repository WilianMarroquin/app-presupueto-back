<?php


namespace App\Filters\Transactions;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class TransactionDateRangeFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        $value = explode(' ', $value);
        if ($value[0]) {
            $query->whereDate('transaction_date', '>=', $value[0]);
        }
        if (isset($value[1])) {
            $query->whereDate('transaction_date', '<=', $value[1]);
        }

        return $query;
    }
}
