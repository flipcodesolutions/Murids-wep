<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OnboardingStep;
use App\Models\Religion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OnboardingStepController extends Controller
{
    public function index()
    {

        return view('admin.onboarding.index');
    }

    public function create()
    {
        $religions = Religion::where('status', 1)->orderBy('name')->get();

        return view('admin.onboarding.create', compact('religions', 'timeSlots'));
    }

    public function edit($id)
    {
        return view('admin.onboarding.edit', compact('id'));
    }

    public function fetch(): JsonResponse
    {
        $onboardingSteps = OnboardingStep::with(['religion'])
            ->orderByDesc('id')
            ->paginate(10);

        return response()->json($onboardingSteps);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255|unique:questions,question',
            'religion_id' => 'required|exists:religions,id',
            'option' => 'required|array|min:1',
            'option.*' => 'required|string|max:255',
            'status' => 'nullable|in:0,1',
        ]);

        OnboardingStep::create([
            'question' => $validated['question'],
            'religion_id' => $validated['religion_id'],
            'option' => $validated['option'],
            'status' => $validated['status'] ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Onboarding step created successfully.',
        ]);
    }

    public function update(Request $request, OnboardingStep $onboardingStep): JsonResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255|unique:questions,question,'.$onboardingStep->id,
            'religion_id' => 'required|exists:religions,id',
            'option' => 'required|array|min:1',
            'option.*' => 'required|string|max:255',
            'status' => 'nullable|in:0,1',
        ]);

        $onboardingStep->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Onboarding step updated successfully.',
        ]);
    }

    public function destroy(OnboardingStep $onboardingStep): JsonResponse
    {
        $onboardingStep->delete();

        return response()->json([
            'success' => true,
            'message' => 'Onboarding step deleted successfully.',
        ]);
    }
}
