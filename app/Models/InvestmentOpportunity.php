<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentOpportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'target_amount',
        'minimum_investment',
        'expected_return',
        'duration_months',
        'status',
        'start_date',
        'end_date',
    ];
}