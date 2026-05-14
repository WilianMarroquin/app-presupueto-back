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
 * @property int $budget_template_id
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property int $is_active
 * @property float $total_budgeted
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property-read \App\Models\BudgetTemplate $budgetTemplate
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriod newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriod newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriod query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriod whereBudgetTemplateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriod whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriod whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriod whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriod whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriod whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriod whereTotalBudgeted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriod whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|BudgetPeriod whereUserId($value)
 * @mixin \Eloquent
 */
class BudgetPeriod extends Model
{


    use HasFactory;

    protected $table = 'budget_periods';


    protected $fillable = [
        'user_id',
        'budget_template_id',
        'start_date',
        'end_date',
        'is_active',
        'total_budgeted'
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'budget_template_id' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'integer',
        'total_budgeted' => 'float',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
    ];


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required|integer',
        'budget_template_id' => 'required|integer',
        'start_date' => 'required|date',
        'end_date' => 'required|date',
        'is_active' => 'required|integer',
        'total_budgeted' => 'required|numeric',
    ];

    public static $atributosAdicionalesPorScope = [];

    protected function getArrayableAppends(): array
    {
        if (count(self::$atributosAdicionalesPorScope) > 0) {
            $this->appends = array_merge($this->appends, self::$atributosAdicionalesPorScope);
            return $this->appends;
        }


        return parent::getArrayableAppends();
    }

    public function scopeConAtributoAdicional($query, $atributoAdicionalNombre)
    {
        if (is_array($atributoAdicionalNombre)) {
            self::$atributosAdicionalesPorScope = array_merge(self::$atributosAdicionalesPorScope, $atributoAdicionalNombre);
            return $query;
        } else {
            self::$atributosAdicionalesPorScope[] = $atributoAdicionalNombre;
        }

        return $query;
    }

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
    public function budgetTemplate(): BelongsTo
    {
        return $this->belongsTo(BudgetTemplate::class, 'budget_template_id', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getTextoAttribute(): string
    {
        $fechaFin = $this->end_date ? $this->end_date->format('d-m-Y') : 'Actualidad';

        return ' (' . $this->start_date->format('d-m-Y') . ' - ' . $fechaFin . ')';
    }

}
