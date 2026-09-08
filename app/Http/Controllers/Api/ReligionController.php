<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Religion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Religions", description: "API Endpoints for Religions")]
class ReligionController extends Controller
{
    #[OA\Get(
        path: "/api/religions",
        summary: "Get list of religions",
        description: "Returns list of all religions",
        operationId: "getReligionsList",
        tags: ["Religions"],
        parameters: [
            new OA\Parameter(
                name: "status",
                in: "query",
                required: false,
                description: "Filter by status (1 for active, 0 for inactive)",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Religion::query();

        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $religions = $query->orderBy('id', 'asc')->get()->map(function ($religion) {
            if ($religion->image_url) {
                $religion->image = $religion->image_url;
            }
            return $religion;
        });

        return response()->json([
            'success' => true,
            'message' => 'Religions fetched successfully.',
            'data' => $religions,
        ], 200);
    }
}
