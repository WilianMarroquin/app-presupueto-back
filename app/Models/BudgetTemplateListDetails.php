<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $budget_item_id
 * @property string|null $model_type
 * @property int|null $model_id
 * @property-read \App\Models\BudgetItem $budgetItem
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplateListDetails newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplateListDetails newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplateListDetails query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplateListDetails whereBudgetItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplateListDetails whereModelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplateListDetails whereModelType($value)
 * @mixin \Eloquent
 */
class BudgetTemplateListDetails extends Model
{


    use HasFactory;

    protected $table = 'budget_tamplate_item_has_details';


    protected $fillable = [
        'budget_item_id',
        'model_type',
        'model_id'
    ];

    protected $appends = ['es_gasto_fijo'];

    public $timestamps = false;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'budget_item_id' => 'integer',
        'model_type' => 'string',
        'model_id' => 'integer',
    ];


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'budget_item_id' => 'required|integer',
        'model_type' => 'nullable|string|max:255',
        'model_id' => 'nullable|integer',
    ];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages = [

    ];


    /**
     * Accessor for relationships
     *
     * @var array
     */
    public function budgetItem(): BelongsTo
    {
        return $this->belongsTo(BudgetItem::class, 'budget_item_id', 'id');
    }

    public function model(): BelongsTo
    {
        return $this->morphTo('model');
    }

    public function getEsGastoFijoAttribute(): bool
    {
        return $this->model_type == FixedExpense::class;
    }

}
