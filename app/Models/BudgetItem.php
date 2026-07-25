<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property int $budget_template_id
 * @property int $transaction_category_id
 * @property float $category_limit
 * @property string|null $notes
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property-read \App\Models\TransactionCategory $transactionCategory
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItem whereBudgetTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItem whereCategoryLimit($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItem whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItem whereUpdatedAt($value)
 * @property-read \App\Models\BudgetTemplate $budgetTemplate
 * @property-read \App\Models\TransactionCategory $category
 * @property-read mixed $monto_total_details
 * @property-read \App\Models\BudgetTemplate $template
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BudgetItemDetail> $details
 * @property-read int|null $details_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FixedExpense> $fixedExpenses
 * @property-read int|null $fixed_expenses_count
 * @mixin \Eloquent
 */
class BudgetItem extends Model
{


    use HasFactory;

    protected $table = 'budget_items';


    protected $fillable = [
        'budget_template_id',
        'transaction_category_id',
        'category_limit',
        'notes'
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'budget_template_id' => 'integer',
        'transaction_category_id' => 'integer',
        'category_limit' => 'float',
        'notes' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'budget_template_id' => 'required|integer',
        'transaction_category_id' => 'required|integer',
        'category_limit' => 'required|numeric',
        'notes' => 'nullable|string',
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
    public function template(): BelongsTo
    {
        return $this->belongsTo(BudgetTemplate::class, 'budget_template_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'transaction_category_id', 'id');
    }

    public function getMontoTotalDetailsAttribute()
    {
        return $this->hasMany(BudgetItemDetail::class, 'budget_item_id', 'id')
            ->sum('amount');
    }

    public function fixedExpenses(): BelongsToMany
    {
        return $this->belongsToMany(FixedExpense::class,
            'budget_tamplate_item_has_fixed_expense',
            'budget_item_id',
            'fixed_expense_id'
        );
    }

    public function details(): HasMany
    {
        return $this->hasMany(BudgetItemDetail::class, 'budget_item_id', 'id');
    }

}
