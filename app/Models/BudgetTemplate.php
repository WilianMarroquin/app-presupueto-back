<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @property string $budget_period_type_id
 * @property float $total_estimated_amount
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplate whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplate wherePeriodType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplate whereTotalEstimatedAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplate whereUserId($value)
 * @property int $budget_budget_period_type_id_id
 * @property-read \App\Models\BudgetPeriodType|null $period
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetTemplate whereBudgetPeriodTypeId($value)
 * @property-read bool $esta_activa
 * @property-read mixed $total_expense_amount
 * @property-read mixed $total_income_amount
 * @property-read mixed $total_saving
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BudgetItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\BudgetPeriodType $periodType
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\BudgetPeriod> $periods
 * @property-read int|null $periods_count
 * @mixin \Eloquent
 */
class BudgetTemplate extends Model
{


    use HasFactory;

    protected $table = 'budget_templates';


    protected $fillable = [
        'user_id',
        'name',
        'description',
        'budget_period_type_id',
        'total_estimated_amount'
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'budget_period_type_id' => 'string',
        'total_estimated_amount' => 'float',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'integer',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'budget_period_type_id' => 'required|integer',
        'total_estimated_amount' => 'numeric',
    ];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages = [

    ];

    protected $appends = [
        'total_expense_amount',
        'total_income_amount',
        'total_saving',
        'esta_activa'
    ];

    /**
     * Accessor for relationships
     *
     * @var array
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function periodType(): BelongsTo
    {
        return $this->belongsTo(BudgetPeriodType::class, 'budget_period_type_id', 'id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BudgetItem::class, 'budget_template_id', 'id');
    }

    public function periods(): HasMany
    {
        return $this->hasMany(BudgetPeriod::class, 'budget_template_id', 'id');
    }

    public function getTotalExpenseAmountAttribute()
    {
        return $this->items()
            ->whereHas('category', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery->where('type', TransactionCategory::CATEGORY_TYPE_EXPENSE)
                        ->orWhere('id', TransactionCategory::AHORRO_Y_METAS);
                });
            })->sum('category_limit');
    }

    public function getTotalIncomeAmountAttribute()
    {
        return $this->items()
            ->whereHas('category', function ($query) {
                $query->where('type', TransactionCategory::CATEGORY_TYPE_INCOME);
            })->sum('category_limit');
    }

    public function getTotalSavingAttribute()
    {
        return $this->items()->where('transaction_category_id', TransactionCategory::AHORRO_Y_METAS)
            ->sum('category_limit');
    }

    public function getEstaActivaAttribute(): bool
    {
        $currentDate = now();

        return $this->periods()
            ->where('start_date', '<=', $currentDate)
            ->whereNull('end_date')
            ->where('is_active', true)
            ->exists();
    }

}
