<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Religion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function fetch(Request $request): JsonResponse
    {
        $query = Religion::orderByDesc('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->input('per_page', 10);

        return response()->json($query->paginate($perPage));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:religions,name',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
            'status' => 'nullable',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
            $targetDir = public_path('images/religions');
            if (!file_exists($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }
            $file->move($targetDir, $filename);
            $validated['image'] = 'religions/' . $filename;
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
            'image' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,svg|max:2048',
            'status' => 'nullable',
        ]);

        if ($request->hasFile('image')) {
            $this->deleteImage($religion->image);

            $file = $request->file('image');
            $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $file->getClientOriginalExtension();
            $targetDir = public_path('images/religions');
            if (!file_exists($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }
            $file->move($targetDir, $filename);
            $validated['image'] = 'religions/' . $filename;
        }

        $validated['status'] = $request->boolean('status', true);

        $religion->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Religion updated successfully.',
            'data' => $religion->fresh(),
        ]);
    }

    public function image(Religion $religion)
    {
        if ($religion->image_url) {
            return redirect($religion->image_url);
        }

        $noImagePath = public_path('images/no-image.png');
        if (file_exists($noImagePath)) {
            return response()->file($noImagePath);
        }

        return abort(404);
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
        if (!$path) return;

        $cleanPath = ltrim($path, '/');
        if (str_starts_with($cleanPath, 'images/')) {
            $cleanPath = substr($cleanPath, 7);
        }
        if (str_starts_with($cleanPath, 'storage/')) {
            $cleanPath = substr($cleanPath, 8);
        }
        $filename = basename($cleanPath);

        $possibleFiles = [
            public_path('images/religions/' . $filename),
            public_path('images/' . $cleanPath),
            public_path('storage/religions/' . $filename),
            storage_path('app/public/religions/' . $filename),
        ];

        foreach ($possibleFiles as $file) {
            if (file_exists($file) && is_file($file)) {
                @unlink($file);
            }
        }
    }
}
