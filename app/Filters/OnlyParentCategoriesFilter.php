<?php


namespace App\Filters;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class OnlyParentCategoriesFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property)
    {
        if (filter_var($value, FILTER_VALIDATE_BOOLEAN)) {
            return $query->whereNull('parent_id');
        }

        return $query;
    }
}
