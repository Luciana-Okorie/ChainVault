<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InvestmentApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvestmentApplicationController extends Controller
{
    /**
     * Display all investment applications for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $applications = InvestmentApplication::with('investmentOpportunity')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $applications,
        ]);
    }

    /**
     * Create a new investment application.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'investment_opportunity_id' => [
                'required',
                'exists:investment_opportunities,id',
            ],
            'amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $existingApplication = InvestmentApplication::where('user_id', $request->user()->id)
            ->where('investment_opportunity_id', $validated['investment_opportunity_id'])
            ->first();

        if ($existingApplication) {
            return response()->json([
                'success' => false,
                'message' => 'You have already applied to this investment opportunity.',
            ], 409);
        }

        $application = InvestmentApplication::create([
            'user_id' => $request->user()->id,
            'investment_opportunity_id' => $validated['investment_opportunity_id'],
            'amount' => $validated['amount'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Investment application submitted successfully.',
            'data' => $application->load('investmentOpportunity'),
        ], 201);
    }

    /**
     * Display a specific investment application.
     */
    public function show(Request $request, InvestmentApplication $investmentApplication): JsonResponse
    {
        if ($investmentApplication->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $investmentApplication->load('investmentOpportunity'),
        ]);
    }

    /**
     * Update an investment application.
     */
    public function update(
        Request $request,
        InvestmentApplication $investmentApplication
    ): JsonResponse {
        if ($investmentApplication->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $validated = $request->validate([
            'amount' => [
                'sometimes',
                'numeric',
                'min:0.01',
            ],
            'status' => [
                'sometimes',
                'in:pending,approved,rejected',
            ],
            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        $investmentApplication->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Investment application updated successfully.',
            'data' => $investmentApplication->fresh()->load('investmentOpportunity'),
        ]);
    }

    /**
     * Delete an investment application.
     */
    public function destroy(
        Request $request,
        InvestmentApplication $investmentApplication
    ): JsonResponse {
        if ($investmentApplication->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 403);
        }

        $investmentApplication->delete();

        return response()->json([
            'success' => true,
            'message' => 'Investment application deleted successfully.',
        ]);
    }
}