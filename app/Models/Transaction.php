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
 * @property int $category_id
 * @property int $account_id
 * @property float $amount
 * @property string $description
 * @property \Illuminate\Support\Carbon $transaction_date
 * @property int $payment_method_id
 * @property int $is_recurring

 * @property int|null $created_ad
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property-read \App\Models\Account $account
 * @property-read \App\Models\TransactionCategory $transactionCategory
 * @property-read \App\Models\TransactionPaymentMethod $transactionPaymentMethod
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereCreatedAd($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereIsRecurring($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction wherePaymentMethodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Transaction withoutTrashed()
 * @mixin \Eloquent
 */
class Transaction extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'transactions';


    protected $fillable =
        [
    'category_id',
    'account_id',
    'amount',
    'description',
    'transaction_date',
    'payment_method_id',
    'is_recurring',
];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
        'id' => 'integer',
        'category_id' => 'integer',
        'account_id' => 'integer',
        'amount' => 'float',
        'description' => 'string',
        'transaction_date' => 'date',
        'payment_method_id' => 'integer',
        'is_recurring' => 'boolean',
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
    'category_id' => 'required|integer',
    'account_id' => 'required|integer',
    'amount' => 'required|numeric',
    'description' => 'required|string',
    'payment_method_id' => 'required|integer',
    'is_recurring' => 'required|boolean',
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
    public function account(): BelongsTo
    {
    return $this->belongsTo(Account::class,'account_id','id');
    }

    public function category(): BelongsTo
    {
    return $this->belongsTo(TransactionCategory::class,'category_id','id');
    }

    public function paymentMethod(): BelongsTo
    {
    return $this->belongsTo(TransactionPaymentMethod::class,'payment_method_id','id');
    }

}
