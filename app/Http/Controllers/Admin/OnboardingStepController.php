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

        return view('admin.onboarding.create', compact('religions'));
    }

    public function edit($id)
    {
        $religions = Religion::where('status', 1)->orderBy('name')->get();
        $onboardingStep = OnboardingStep::with(['religion'])
            ->where('id', $id)
            ->firstOrFail();

        return view('admin.onboarding.edit', compact('religions', 'onboardingStep'));
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
            'question' => 'required|string|max:255',
            'religion_id' => 'required|exists:religions,id',
            'options' => 'required|array|min:1',
            'options.*' => 'required|string|max:255',
        ]);

        OnboardingStep::create([
            'question' => $validated['question'],
            'religion_id' => $validated['religion_id'],
            'options' => $validated['options'],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Onboarding step created successfully.',
        ]);
    }

    public function update(Request $request, OnboardingStep $onboardingStep): JsonResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'religion_id' => 'required|exists:religions,id',
            'options' => 'required|array|min:1',
            'options.*' => 'required|string|max:255',
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
