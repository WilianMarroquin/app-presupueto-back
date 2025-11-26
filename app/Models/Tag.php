<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Tag extends Model
{
    protected $fillable = ['name', 'slug', 'type', 'color'];

    /**
     * Obtener todas las categorías que tienen este tag.
     */
    public function categories(): MorphToMany
    {
        return $this->morphedByMany(TransactionCategory::class, 'taggable');
    }

    /**
     * Obtener todas las transacciones que tienen este tag.
     */
    public function transactions(): MorphToMany
    {
        return $this->morphedByMany(Transaction::class, 'taggable');
    }
}
