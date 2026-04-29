<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @property string $period_type
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
        'period_type',
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
        'period_type' => 'string',
        'total_estimated_amount' => 'float',
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
            'user_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'period_type' => 'required|string|max:255',
            'total_estimated_amount' => 'required|numeric',
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(BudgetPeriodType::class, 'period_type', 'name');
    }

}
