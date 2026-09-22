<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OnboardingStep;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Onboarding Steps", description: "API Endpoints for Onboarding Steps")]
class OnboardingStepController extends Controller
{
    #[OA\Get(
        path: "/api/onboarding-steps",
        summary: "Get onboarding steps",
        description: "Returns list of onboarding steps filtered by active religions",
        operationId: "getOnboardingSteps",
        tags: ["Onboarding Steps"],
        parameters: [
            new OA\Parameter(
                name: "religion_id",
                in: "query",
                required: false,
                description: "Filter by Religion ID",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Successful operation",
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: "success", type: "boolean", example: true),
                        new OA\Property(property: "message", type: "string", example: "Onboarding steps fetched successfully."),
                        new OA\Property(
                            property: "data",
                            type: "array",
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: "id", type: "integer", example: 1),
                                    new OA\Property(property: "religion_id", type: "integer", example: 1),
                                    new OA\Property(property: "question", type: "string", example: "Are you practicing?"),
                                    new OA\Property(property: "yes_response", type: "string", nullable: true, example: "Yes"),
                                    new OA\Property(property: "no_response", type: "string", nullable: true, example: "No")
                                ]
                            )
                        )
                    ]
                )
            )
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = OnboardingStep::select('id', 'religion_id', 'question', 'yes_response', 'no_response')
            ->whereHas('religion', function ($q) {
                $q->where('status', 1);
            });

        if ($request->filled('religion_id')) {
            $query->where('religion_id', $request->religion_id);
        }

        $onboardingSteps = $query->orderBy('id', 'asc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Onboarding steps fetched successfully.',
            'data' => $onboardingSteps,
        ], 200);
    }
}
