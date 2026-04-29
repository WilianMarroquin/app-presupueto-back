<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property int $budget_item_id
 * @property string $name
 * @property float $amount
 * @property int|null $created_at
 * @property int|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItemDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItemDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItemDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItemDetail whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItemDetail whereBudgetItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItemDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItemDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItemDetail whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetItemDetail whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BudgetItemDetail extends Model
{

    
    use HasFactory;

    protected $table = 'budget_item_details';


    protected $fillable =
        [
    'budget_item_id',
    'name',
    'amount'
];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
        'id' => 'integer',
        'budget_item_id' => 'integer',
        'name' => 'string',
        'amount' => 'float',
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
    'budget_item_id' => 'required|integer',
    'name' => 'required|string|max:255',
    'amount' => 'required|numeric',
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
    public function budgetItem()
    {
    return $this->belongsTo(BudgetItem::class,'budget_item_id','id');
    }

}
