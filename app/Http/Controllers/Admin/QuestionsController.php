<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Religion;
use App\Models\TimeSlot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionsController extends Controller
{
    public function index()
    {

        return view('admin.questions.index');
    }

    public function create()
    {
        $religions = Religion::where('status', 1)->orderBy('name')->get();
        $timeSlots = TimeSlot::orderBy('id')->get();

        return view('admin.questions.create', compact('religions', 'timeSlots'));
    }

    public function edit($id)
    {
        return view('admin.questions.edit', compact('id'));
    }

    public function fetch(Request $request): JsonResponse
    {
        $query = Question::with(['religion', 'timeSlot'])->orderByDesc('id');

        if ($request->filled('religion_id')) {
            $query->where('religion_id', $request->religion_id);
        }

        if ($request->filled('time_slot_id')) {
            $query->where('time_slot_id', $request->time_slot_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('question', 'like', "%{$search}%");
        }

        $perPage = (int) $request->input('per_page', 10);

        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255|unique:questions,question',
            'religion_id' => 'required|exists:religions,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'status' => 'nullable|in:0,1',
        ]);

        Question::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Question created successfully.',
        ]);
    }

    public function update(Request $request, Question $question): JsonResponse
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255|unique:questions,question,'.$question->id,
            'religion_id' => 'required|exists:religions,id',
            'time_slot_id' => 'required|exists:time_slots,id',
            'status' => 'nullable|in:0,1',
        ]);

        $question->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Question updated successfully.',
        ]);
    }

    public function destroy(Question $question): JsonResponse
    {
        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Question deleted successfully.',
        ]);
    }
}
