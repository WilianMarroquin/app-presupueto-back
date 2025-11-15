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
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountCurrency newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountCurrency newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountCurrency onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountCurrency query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountCurrency whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountCurrency whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountCurrency whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountCurrency whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountCurrency whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountCurrency withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AccountCurrency withoutTrashed()
 * @mixin \Eloquent
 */
class AccountCurrency extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'account_currencys';

    const QUETZAL = 1;
    const USD = 2;

    protected $fillable =
        [
    'name'
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
