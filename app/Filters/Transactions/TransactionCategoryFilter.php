<?php


namespace App\Filters\Transactions;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class TransactionCategoryFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        if ($value) {
            $query->whereHas('category', function($q) use ($value) {
                $q->where('parent_id', $value);
            });
        }

        return $query;
    }
}
