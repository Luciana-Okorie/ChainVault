<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InvestmentOpportunityController;
use App\Http\Controllers\Api\InvestmentApplicationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource(
        'investment-opportunities',
        InvestmentOpportunityController::class
    );

    Route::apiResource(
        'investment-applications',
        InvestmentApplicationController::class
    );
});