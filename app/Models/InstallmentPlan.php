<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property float $total_amount
 * @property int $total_installments
 * @property float $interest_rate
 * @property \Illuminate\Support\Carbon $start_date
 * @property string $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstallmentPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstallmentPlan newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstallmentPlan onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstallmentPlan query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstallmentPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstallmentPlan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstallmentPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstallmentPlan whereInterestRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstallmentPlan whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstallmentPlan whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstallmentPlan whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstallmentPlan whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstallmentPlan whereTotalInstallments($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstallmentPlan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstallmentPlan withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InstallmentPlan withoutTrashed()
 * @mixin \Eloquent
 */
class InstallmentPlan extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'installment_plans';


    protected $fillable = [
        'name',
        'total_amount',
        'total_installments',
        'interest_rate',
        'start_date',
        'status'
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'total_amount' => 'float',
        'total_installments' => 'integer',
        'interest_rate' => 'float',
        'start_date' => 'date',
        'status' => 'string',
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
        'name' => 'required|string|max:255',
        'total_amount' => 'required|numeric',
        'total_installments' => 'required|integer',
        'interest_rate' => 'required|numeric',
    ];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages = [

    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'completed';
    const STATUS_CANCELLED = 'cancelled';


    /**
     * Accessor for relationships
     *
     * @var array
     */

    public function paymentsMade(): HasMany
    {
        return $this->hasMany(CreditCardProvisions::class, 'installment_plan_id', 'id')
            ->where('status', CreditCardProvisions::STATUS_SETTLED);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CreditCardProvisions::class, 'installment_plan_id', 'id');
    }

    public function getMonthlyFeeAttribute(): float
    {
        if ($this->total_installments <= 0) {
            return 0;
        }

        $monthlyFee = $this->total_amount / $this->total_installments;

        return round($monthlyFee, 2);
    }

}
