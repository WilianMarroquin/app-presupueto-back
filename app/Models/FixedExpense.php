<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property int $transaction_category_id
 * @property string $name
 * @property string|null $description
 * @property float $base_amount
 * @property int $due_day
 * @property int $is_variable
 * @property int $is_active
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property-read \App\Models\TransactionCategory $transactionCategory
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpense newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpense newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpense onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpense query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpense whereBaseAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpense whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpense whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpense whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpense whereDueDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpense whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpense whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpense whereIsVariable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpense whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpense whereTransactionCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpense whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpense withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpense withoutTrashed()
 * @property-read \App\Models\TransactionCategory $category
 * @property-read bool $is_currently_paid
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\FixedExpensePeriodPayment> $paidPeriods
 * @property-read int|null $paid_periods_count
 * @method static Builder<static>|FixedExpense conAtributoAdicional($atributoAdicionalNombre)
 * @mixin \Eloquent
 */
class FixedExpense extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'fixed_expenses';


    protected $fillable = [
        'transaction_category_id',
        'name',
        'description',
        'base_amount',
        'due_day',
        'is_variable',
        'is_active'
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'transaction_category_id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'base_amount' => 'float',
        'due_day' => 'integer',
        'is_variable' => 'integer',
        'is_active' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'transaction_category_id' => 'required|integer',
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'base_amount' => 'required|numeric',
        'due_day' => 'required|integer',
        'is_variable' => 'required|integer',
        'is_active' => 'required|integer',
    ];

//    protected $appends = [
//        'is_currently_paid'
//    ];

    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages = [

    ];

    public static $atributosAdicionalesPorScope = [];

    protected function getArrayableAppends(): array
    {
        if (count(self::$atributosAdicionalesPorScope) > 0) {
            $this->appends = array_merge($this->appends, self::$atributosAdicionalesPorScope);
            return $this->appends;
        }


        return parent::getArrayableAppends();
    }

    public function scopeConAtributoAdicional($query, $atributoAdicionalNombre)
    {
        if (is_array($atributoAdicionalNombre)) {
            self::$atributosAdicionalesPorScope = array_merge(self::$atributosAdicionalesPorScope, $atributoAdicionalNombre);
            return $query;
        } else {
            self::$atributosAdicionalesPorScope[] = $atributoAdicionalNombre;
        }

        return $query;
    }


    /**
     * Accessor for relationships
     *
     * @var array
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'transaction_category_id', 'id');
    }

    public function paidPeriods(): HasMany
    {
        return $this->hasMany(FixedExpensePeriodPayment::class, 'fixed_expense_id', 'id');
    }

    public function getIsCurrentlyPaidAttribute(): bool
    {
        $periodoActual = currentAccountingPeriod();

        return $this->paidPeriods()
            ->where('budget_period_id', $periodoActual->id)
            ->exists();
    }

}
