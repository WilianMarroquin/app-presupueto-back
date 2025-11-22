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
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPaymentMethod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPaymentMethod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPaymentMethod onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPaymentMethod query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPaymentMethod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPaymentMethod whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPaymentMethod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPaymentMethod whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPaymentMethod whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPaymentMethod withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionPaymentMethod withoutTrashed()
 * @mixin \Eloquent
 */
class TransactionPaymentMethod extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'transaction_payment_methods';


    protected $fillable = [
        'name'
    ];

    const EFECTIVO = 1;
    const TARJETA_DE_DEBITO = 2;
    const TRANSFERENCIA = 3;

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
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
        'name' => 'required|string|max:100',
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


}
