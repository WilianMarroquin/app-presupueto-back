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
 * @property float $amount
 * @property int $period_types_id
 * @property int $category_id
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property-read \App\Models\BudgetPeriodType $budgetPeriodType
 * @property-read \App\Models\TransactionCategory $transactionCategory
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget wherePeriodTypesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Budget withoutTrashed()
 * @mixin \Eloquent
 */
class Budget extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'budgets';

    protected $fillable = [
        'amount',
        'period_types_id',
        'category_id',
        'start_date',
        'end_date'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'amount' => 'float',
        'period_types_id' => 'integer',
        'category_id' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
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
        'amount' => 'required|numeric',
        'period_types_id' => 'required|integer',
        'category_id' => 'required|integer',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
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
    public function periodType(): BelongsTo
    {
        return $this->belongsTo(BudgetPeriodType::class, 'period_types_id', 'id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id', 'id');
    }

}
