<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 *
 *
 * @property int $id
 * @property int $installment_plan_id
 * @property int $installment_number
 * @property int $month
 * @property int $year
 * @property float $amount
 * @property string $status
 * @property int|null $transaction_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property-read \App\Models\InstallmentPlan $installmentPlan
 * @property-read \App\Models\Transaction|null $transaction
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardProvisions newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardProvisions newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardProvisions onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardProvisions query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardProvisions whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardProvisions whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardProvisions whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardProvisions whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardProvisions whereInstallmentNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardProvisions whereInstallmentPlanId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardProvisions whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardProvisions whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardProvisions whereTransactionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardProvisions whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardProvisions whereYear($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardProvisions withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardProvisions withoutTrashed()
 * @mixin \Eloquent
 */
class CreditCardProvisions extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'credit_card_provisions';


    protected $fillable =
        [
            'installment_plan_id',
            'installment_number',
            'month',
            'year',
            'amount',
            'status',
            'transaction_id'
        ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
            'id' => 'integer',
            'installment_plan_id' => 'integer',
            'installment_number' => 'integer',
            'month' => 'integer',
            'year' => 'integer',
            'amount' => 'float',
            'status' => 'string',
            'transaction_id' => 'integer',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
        ];


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules =
        [
            'installment_plan_id' => 'required|integer|unique:credit_card_provisions,installment_plan_id',
            'installment_number' => 'required|integer|unique:credit_card_provisions,installment_number',
            'month' => 'required|integer',
            'year' => 'required|integer',
            'amount' => 'required|numeric',
            'status' => 'required|string',
            'transaction_id' => 'nullable|integer',
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
    public function plan(): BelongsTo
    {
        return $this->belongsTo(InstallmentPlan::class, 'installment_plan_id', 'id');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_id', 'id');
    }

}
