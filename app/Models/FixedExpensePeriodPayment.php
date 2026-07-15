<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property int $budget_period_id
 * @property int $transaction_id
 * @property int $fixed_expense_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property-read \App\Models\BudgetPeriod $budgetPeriod
 * @property-read \App\Models\Transaction $transaction
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpensePeriodPayment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpensePeriodPayment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpensePeriodPayment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpensePeriodPayment whereBudgetPeriodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpensePeriodPayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpensePeriodPayment whereFixedExpenseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpensePeriodPayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpensePeriodPayment whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|FixedExpensePeriodPayment whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FixedExpensePeriodPayment extends Model
{

    
    use HasFactory;

    protected $table = 'fixed_expenses_period_paymets';


    protected $fillable =
        [
    'budget_period_id',
    'transaction_id',
    'fixed_expense_id'
];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
        'id' => 'integer',
        'budget_period_id' => 'integer',
        'transaction_id' => 'integer',
        'fixed_expense_id' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];



    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules =
    [
    'budget_period_id' => 'required|integer',
    'transaction_id' => 'required|integer',
    'fixed_expense_id' => 'required|integer',
];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages =[

    ];


    /**
     * Accessor for relationships
     *
     * @var array
     */
    public function budgetPeriod()
    {
    return $this->belongsTo(BudgetPeriod::class,'budget_period_id','id');
    }

    public function fixedExpense()
    {
    return $this->belongsTo(FixedExpense::class,'fixed_expense_id','id');
    }

    public function transaction()
    {
    return $this->belongsTo(Transaction::class,'transaction_id','id');
    }

}
