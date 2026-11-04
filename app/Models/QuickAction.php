<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuickAction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'quick_actions';

    protected $fillable = [
        'name',
        'category_id',
        'account_id',
        'payment_method_id',
        'amount',
        'description',
        'icon',
        'color',
    ];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'category_id' => 'integer',
        'account_id' => 'integer',
        'payment_method_id' => 'integer',
        'amount' => 'float',
        'description' => 'string',
        'icon' => 'string',
        'color' => 'string',
    ];

    public static array $rules = [
        'name' => 'required|string|max:255',
        'category_id' => 'required|integer|exists:transaction_categories,id',
        'account_id' => 'nullable|integer|exists:accounts,id',
        'payment_method_id' => 'nullable|integer|exists:transaction_payment_methods,id',
        'amount' => 'required|numeric|min:0.01',
        'description' => 'nullable|string',
        'icon' => 'nullable|string|max:100',
        'color' => 'nullable|string|max:30',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id', 'id');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(TransactionPaymentMethod::class, 'payment_method_id', 'id');
    }
}
