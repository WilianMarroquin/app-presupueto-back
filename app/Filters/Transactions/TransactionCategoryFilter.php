<?php


namespace App\Filters\Transactions;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class TransactionCategoryFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        if ($value) {
            $query->where('category_id', $value);
        }

        return $query;
    }
}
