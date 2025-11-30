<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 *
 *
 * @property int $id
 * @property string $name
 * @property int $type_id
 * @property int $currency_id
 * @property float $initial_balance
 * @property float $current_balance
 * @property int $is_active
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property-read \App\Models\AccountCurrency $accountCurrency
 * @property-read \App\Models\AccountType $accountType
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereCurrencyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereCurrentBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereInitialBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account withoutTrashed()
 * @property-read \App\Models\AccountType $type
 * @property-read \App\Models\AccountCurrency $currency
 * @property string $nature
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereNature($value)
 * @property-read \App\Models\CreditCardDetail|null $creditCardDetail
 * @property string $description
 * @property-read string $text
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Transaction> $transactionsPending
 * @property-read int|null $transactions_pending_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Account whereDescription($value)
 * @mixin \Eloquent
 */
class Account extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'accounts';


    protected $fillable = [
        'name',
        'type_id',
        'currency_id',
        'initial_balance',
        'current_balance',
        'is_active',
        'nature',
        'bank_name',
        'description',
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
            'id' => 'integer',
            'name' => 'string',
            'type_id' => 'integer',
            'currency_id' => 'integer',
            'initial_balance' => 'float',
            'current_balance' => 'float',
            'is_active' => 'integer',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'deleted_at' => 'timestamp',
        ];

    protected $appends = [
        'text',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
            'name' => 'required|string|max:45',
            'type_id' => 'required|integer',
            'currency_id' => 'required|integer',
            'initial_balance' => 'required|numeric',
            'current_balance' => 'required|numeric',
            'is_active' => 'required|integer',
            'description' => 'required|string',
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
    public function currency(): BelongsTo
    {
        return $this->belongsTo(AccountCurrency::class, 'currency_id', 'id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AccountType::class, 'type_id', 'id');
    }

    public function withdraw(float $amount): void
    {
        if ($this->nature === 'liability') {
            $this->current_balance += $amount;
        } else {
            if ($this->current_balance < $amount) {
                throw new \Exception('Saldo insuficiente para retirar ' . $amount . ' de la cuenta ' . $this->name);
            }
            $this->current_balance -= $amount;
        }

        $this->save();
    }

    /**
     * Procesa una ENTRADA de dinero (Ingreso / Pago a la Tarjeta).
     * * @param float $amount
     * @return void
     */
    public function depositary(float $amount): void
    {
        if ($this->nature === 'liability') {
            $this->current_balance -= $amount;
        } else {
            $this->current_balance += $amount;
        }

        $this->save();
    }

    public function creditCardDetail(): HasOne
    {
        return $this->hasOne(CreditCardDetail::class, 'account_id', 'id');
    }

    public function getTextAttribute(): string
    {
        return $this->name . ' (' . $this->currency->symbol . ')'. ' - ' . $this->description;
    }

    public function transactionsPending(): HasMany
    {
        return $this->hasMany(Transaction::class, 'account_id', 'id')
            ->where('is_settled', 0);
    }

    public function isCreditCard(): bool
    {
        return $this->type_id === AccountType::CREDIT_CARD;
    }
}
