<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvestmentApplication extends Model
{
    protected $fillable = [
        'user_id',
        'investment_opportunity_id',
        'amount',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function investmentOpportunity(): BelongsTo
    {
        return $this->belongsTo(InvestmentOpportunity::class);
    }
}