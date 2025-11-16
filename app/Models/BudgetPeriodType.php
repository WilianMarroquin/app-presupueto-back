<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriodType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriodType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriodType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriodType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriodType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriodType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriodType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriodType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriodType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriodType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriodType withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriodType withoutTrashed()
 * @mixin \Eloquent
 */
class BudgetPeriodType extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'budget_period_types';


    protected $fillable =
        [
    'name',
    'description'
];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
        'id' => 'integer',
        'name' => 'string',
        'description' => 'string',
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
    'name' => 'required|string|max:100',
    'description' => 'required|string',
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
    

}
