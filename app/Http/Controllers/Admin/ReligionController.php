<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Religion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReligionController extends Controller
{
    public function create()
    {
        return view('admin.religion.create');
    }

    public function index()
    {
        return view('admin.religion.index');
    }

    public function edit(Religion $religion)
    {
        return view('admin.religion.edit', compact('religion'));
    }

    public function fetch(): JsonResponse
    {
        $religions = Religion::orderByDesc('id')->get();

        return response()->json([
            'success' => true,
            'data' => $religions,
        ]);
    }

    public function image(Religion $religion): StreamedResponse
    {
        if (! $religion->image || ! Storage::disk('local')->exists($religion->image)) {
            abort(404);
        }

        return Storage::disk('local')->response($religion->image);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:religions,name',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'status' => 'nullable',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('religions', 'local');
        }

        $validated['status'] = $request->boolean('status', true);

        $religion = Religion::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Religion created successfully.',
            'data' => $religion,
        ], 201);
    }

    public function update(Request $request, Religion $religion): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:religions,name,'.$religion->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'status' => 'nullable',
        ]);

        if ($request->hasFile('image')) {
            $this->deleteImage($religion->image);
            $validated['image'] = $request->file('image')->store('religions', 'local');
        }

        $validated['status'] = $request->boolean('status', true);

        $religion->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Religion updated successfully.',
            'data' => $religion->fresh(),
        ]);
    }

    public function destroy(Religion $religion): JsonResponse
    {
        $this->deleteImage($religion->image);
        $religion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Religion deleted successfully.',
        ]);
    }

    private function deleteImage(?string $path): void
    {
        if ($path && Storage::disk('local')->exists($path)) {
            Storage::disk('local')->delete($path);
        }
    }
}
