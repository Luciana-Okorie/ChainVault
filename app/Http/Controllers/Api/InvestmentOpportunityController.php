<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvestmentOpportunity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvestmentOpportunityController extends Controller
{
    public function index(): JsonResponse
    {
        $opportunities = InvestmentOpportunity::latest()->get();

        return response()->json([
            'success' => true,
            'data' => $opportunities,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'target_amount' => ['required', 'numeric', 'min:0'],
            'minimum_investment' => ['required', 'numeric', 'min:0'],
            'expected_return' => ['required', 'numeric', 'min:0'],
            'duration_months' => ['required', 'integer', 'min:1'],
            'status' => ['sometimes', 'in:draft,open,funded,closed'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $opportunity = InvestmentOpportunity::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Investment opportunity created successfully.',
            'data' => $opportunity,
        ], 201);
    }

    public function show(InvestmentOpportunity $investmentOpportunity): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $investmentOpportunity,
        ]);
    }

    public function update(
        Request $request,
        InvestmentOpportunity $investmentOpportunity
    ): JsonResponse {
        $validated = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'target_amount' => ['sometimes', 'numeric', 'min:0'],
            'minimum_investment' => ['sometimes', 'numeric', 'min:0'],
            'expected_return' => ['sometimes', 'numeric', 'min:0'],
            'duration_months' => ['sometimes', 'integer', 'min:1'],
            'status' => ['sometimes', 'in:draft,open,funded,closed'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
        ]);

        $investmentOpportunity->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Investment opportunity updated successfully.',
            'data' => $investmentOpportunity->fresh(),
        ]);
    }

    public function destroy(InvestmentOpportunity $investmentOpportunity): JsonResponse
    {
        $investmentOpportunity->delete();

        return response()->json([
            'success' => true,
            'message' => 'Investment opportunity deleted successfully.',
        ]);
    }
}