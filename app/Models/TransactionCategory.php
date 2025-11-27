<?php

namespace App\Models;


use App\Traits\HasTags;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
 * @property-read TransactionCategory|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, TransactionCategory> $subCategories
 * @property-read int|null $sub_categories_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionCategory whereName($value)
 * @property string|null $icon
 * @property string|null $color
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tag> $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionCategory whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransactionCategory whereIcon($value)
 * @mixin \Eloquent
 */
class TransactionCategory extends Model
{

    use SoftDeletes;
    use HasFactory;
    use HasTags;

    protected $table = 'transaction_categories';

    const SALARIO = 1;
    const NEGOCIOS = 2;
    const INVERSIONES = 3;
    const OTROS_INGRESOS = 4;
    const VIVIENDA = 5;
    const ALIMENTACION = 6;
    const TRANSPORTE = 7;
    const Salud_Y_Bienestar = 8;
    const OCIO_Y_SOCIAL = 9;
    const COMPRAS = 10;
    const EDUCACION = 11;
    const FINANZAS = 12;


    protected $fillable = [
        'name',
        'type',
        'description',
        'icon',
        'color',
        'parent_id'
    ];

    const CATEGORY_TYPE_INCOME = 'Income';
    const CATEGORY_TYPE_EXPENSE = 'Expense';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'type' => 'string',
        'description' => 'string',
        'icon' => 'string',
        'color' => 'string',
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
    public static $rules = [
        'name' => 'required|string|max:100',
        'type' => 'required|string',
        'description' => 'required|string',
        'parent_id' => 'integer',
        'icon' => 'nullable|string|max:100',
        'color' => 'nullable|string|max:100',
    ];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages = [

    ];

    public function isExpense(): bool
    {
        return $this->type === self::CATEGORY_TYPE_EXPENSE;
    }

    public function isIncome(): bool
    {
        return $this->type === self::CATEGORY_TYPE_INCOME;
    }

}
