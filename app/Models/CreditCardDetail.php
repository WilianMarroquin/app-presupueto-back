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
 * @property int $account_id
 * @property string $bank_name
 * @property string|null $alias
 * @property string $network
 * @property string $color
 * @property string $last_4
 * @property int $cutoff_day
 * @property int $payment_day
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property-read \App\Models\Account $account
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail whereBankName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail whereCutoffDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail whereLast4($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail whereNetwork($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail wherePaymentDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail withoutTrashed()
 * @property float $credit_limit
 * @property mixed $0
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CreditCardDetail whereCreditLimit($value)
 * @mixin \Eloquent
 */
class CreditCardDetail extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'credit_card_details';


    protected $fillable = [
        'account_id',
        'alias',
        'network',
        'color',
        'last_4',
        'cutoff_day',
        'payment_day',
        'credit_limit',
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'account_id' => 'integer',
        'alias' => 'string',
        'network' => 'string',
        'color' => 'string',
        'last_4' => 'string',
        'cutoff_day' => 'integer',
        'payment_day' => 'integer',
        'credit_limit',
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
        'account_id' => 'required|integer|unique:credit_card_details,account_id',
        'alias' => 'nullable|string|max:50',
        'network' => 'required|string|max:20',
        'color' => 'required|string|max:7',
        'last_4' => 'required|string|max:4',
        'cutoff_day' => 'required|integer',
        'payment_day' => 'required|integer',
        'credit_limit' => 'required|numeric|min:0',
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
    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

}
