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
 * @property string $type
 * @property string $description
 * @property int $parent_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, TransactionCategory> $transactionCategories
 * @property-read int|null $transaction_categories_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionCategory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionCategory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionCategory onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionCategory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionCategory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionCategory whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionCategory whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionCategory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionCategory whereNombre($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionCategory whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionCategory whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionCategory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionCategory withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionCategory withoutTrashed()
 * @mixin \Eloquent
 */
class TransactionCategory extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'transaction_categories';


    protected $fillable =
        [
    'name',
    'type',
    'description',
    'parent_id'
];

    const CATEGORY_TYPE_INCOME = 'Income';
    const CATEGORY_TYPE_EXPENSE = 'Expense';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
        'id' => 'integer',
        'name' => 'string',
        'type' => 'string',
        'description' => 'string',
        'parent_id' => 'integer',
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
    'type' => 'required|string',
    'description' => 'required|string',
    'parent_id' => 'integer',
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
    public function subCategories()
    {
    return $this->hasMany(TransactionCategory::class,'parent_id','id');
    }

}
