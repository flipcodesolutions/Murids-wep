<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OnboardingStep;
use App\Models\Question;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserProfile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use OpenApi\Attributes as OA;

#[OA\Tag(name: "Users", description: "API Endpoints for Users and Authentication")]
class UsersController extends Controller
{
    #[OA\Get(
        path: "/api/users",
        summary: "Get list of users",
        description: "Returns list of users",
        operationId: "getUsersList",
        tags: ["Users"],
        responses: [
            new OA\Response(response: 200, description: "Successful operation")
        ]
    )]
    public function index(): JsonResponse
    {
        $users = User::orderByDesc('id')->get();

        return response()->json([
            'success' => true,
            'message' => 'Users fetched successfully.',
            'data' => $users,
        ], 200);
    }

    #[OA\Get(
        path: "/api/users/{id}",
        summary: "Get user details by ID",
        description: "Returns user data",
        operationId: "getUserById",
        tags: ["Users"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "User ID", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "Successful operation"),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function show($id): JsonResponse
    {
        $user = User::with('profile')->findOrFail($id);
        if (!$user->profile) {
            $user->setRelation('profile', []);
        }

        return response()->json([
            'success' => true,
            'message' => 'User fetched successfully.',
            'data' => $user,
        ], 200);
    }

    #[OA\Post(
        path: "/api/register",
        summary: "Register a new user",
        description: "Creates a new user account",
        operationId: "registerUser",
        tags: ["Users"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "user_type", "provider", "password", "password_confirmation"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "user_type", type: "string", enum: ["admin", "user"], example: "user"),
                    new OA\Property(property: "provider", type: "string", enum: ["google", "apple", "other"], example: "other"),
                    new OA\Property(property: "provider_id", type: "string", nullable: true, example: "123456789"),
                    new OA\Property(property: "password", type: "string", format: "password", example: "secret123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "secret123")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "User registered successfully"),
            new OA\Response(response: 422, description: "Validation error")
        ]
    )]
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'user_type' => 'required|in:admin,user',
            'provider' => 'required|in:google,apple,other',
            'provider_id' => 'nullable|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully.',
            'data' => $user,
        ], 201);
    }

    #[OA\Post(
        path: "/api/login",
        summary: "Authenticate user and login",
        description: "Logs in a user with email and password",
        operationId: "loginUser",
        tags: ["Users"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "[EMAIL_ADDRESS]"),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Login successful"),
            new OA\Response(response: 401, description: "Invalid credentials")
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name ?? explode('@', $request->email)[0],
                'provider' => $request->provider ?? 'google',
                'provider_id' => $request->provider_id ?? null,
                'user_type' => $request->user_type ?? 'user',
                'email_verified_at' => now(),
            ]
        );

        $userUpdates = array_filter([
            'name' => $request->name,
            'provider' => $request->provider,
            'provider_id' => $request->provider_id,
        ], fn($val) => !is_null($val));

        if (!empty($userUpdates)) {
            $user->update($userUpdates);
        }

        Auth::login($user);
        $token = $user->createToken('auth_token')->plainTextToken;

        $profileFields = $request->only(['religion_id', 'notification_enabled', 'device_token', 'timezone']);

        if ($request->has('profile') && is_array($request->profile)) {
            $profileFields = array_merge(
                $profileFields,
                $request->profile
            );
        }

        $profileData = array_filter($profileFields, fn($value) => !is_null($value));

        if (!empty($profileData)) {
            UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }

        $user->load('profile');
        $profileEmpty = !$user->profile;

        if ($profileEmpty) {
            $user->setRelation('profile', []);
        }

        // Store question answer(s) if provided in login request
        $answersToProcess = [];
        if ($request->has('answers') && is_array($request->answers)) {
            $answersToProcess = $request->answers;
        } elseif ($request->has('question_id') && $request->question_id !== null && $request->question_id !== '') {
            $answersToProcess[] = [
                'question_id' => $request->question_id,
                'religion_id' => $request->religion_id,
                'time_slot_id' => $request->time_slot_id,
                'answer' => $request->answer,
            ];
        }

        foreach ($answersToProcess as $ans) {
            if (isset($ans['question_id']) && $ans['question_id'] !== null && $ans['question_id'] !== '') {
                try {
                    $qId = (int) $ans['question_id'];
                    $rId = $ans['religion_id'] ?? $user->profile?->religion_id ?? 1;
                    $tId = $ans['time_slot_id'] ?? null;

                    if (!Question::where('id', $qId)->exists()) {
                        $onboardingStep = OnboardingStep::find($qId);
                        DB::table('questions')->insertOrIgnore([
                            'id' => $qId,
                            'religion_id' => $onboardingStep->religion_id ?? $rId,
                            'time_slot_id' => $tId,
                            'question' => $onboardingStep->question ?? ('Onboarding Question ' . $qId),
                            'status' => 1
                            
                        ]);
                    }

                    $rawAnswer = $ans['answer'] ?? null;
                    if (is_null($rawAnswer)) {
                        $answerVal = null;
                    } elseif (is_string($rawAnswer)) {
                        $strLower = strtolower(trim($rawAnswer));
                        $answerVal = in_array($strLower, ['yes', 'true', '1'], true) ? 1 : (in_array($strLower, ['no', 'false', '0'], true) ? 0 : (int)$rawAnswer);
                    } else {
                        $answerVal = (int) $rawAnswer;
                    }

                    UserAnswer::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'question_id' => $qId,
                        ],
                        [
                            'religion_id' => $rId,
                            'time_slot_id' => null,
                            'answer' => $answerVal,
                            'answered_at' => now(),
                        ]
                    );
                } catch (\Throwable $e) {
                    \Log::error('UserAnswer save error: ' . $e->getMessage());
                }
            }
        }

        $religionId = $user->profile?->religion_id 
            ?? $request->religion_id 
            ?? ($request->profile['religion_id'] ?? null)
            ?? ($request->answers[0]['religion_id'] ?? null);

        $onboardingSteps = OnboardingStep::query()
            ->when($religionId, function ($q) use ($religionId) {
                $q->where('religion_id', $religionId);
            })
            ->orderBy('id', 'asc')
            ->get();

        $userAnswers = UserAnswer::where('user_id', $user->id)
            ->get()
            ->keyBy('question_id');

        $onboardingSteps->transform(function ($step) use ($userAnswers) {
            $userAnswer = $userAnswers->get($step->id);
            $step->is_answered = !is_null($userAnswer);
            $step->user_answer = $userAnswer ? $userAnswer->answer : null;
            return $step;
        });

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'profile_empty' => $profileEmpty,
            'profile_message' => $profileEmpty
                ? 'User profile is empty. Please update your profile.'
                : 'User profile loaded successfully.',
            'data' => $user,
            'token' => $token,
            'onboarding_steps' => $onboardingSteps,
        ], 200);
    }

    #[OA\Get(
        path: "/api/google/redirect",
        summary: "Redirect to Google OAuth Login",
        description: "Redirects the client to Google's authentication page",
        operationId: "redirectToGoogle",
        tags: ["Users"],
        responses: [
            new OA\Response(response: 302, description: "Redirect to Google OAuth")
        ]
    )]
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // #[OA\Get(
    //     path: "/api/google/callback",
    //     summary: "Google OAuth Callback Redirect URL",
    //     description: "Google OAuth redirect callback endpoint handling user creation/login",
    //     operationId: "handleGoogleCallback",
    //     tags: ["Users"],
    //     responses: [
    //         new OA\Response(response: 200, description: "Google authentication successful"),
    //         new OA\Response(response: 422, description: "Authentication failed")
    //     ]
    // )]
    // public function handleGoogleCallback(Request $request): JsonResponse
    // {
    //     try {
    //         if (!$request->has('code') && !$request->has('access_token')) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Authorization code or access_token is missing from the request. Ensure Google redirects with ?code= or pass access_token parameter.',
    //             ], 400);
    //         }

    //         if ($request->has('access_token')) {
    //             $googleUser = Socialite::driver('google')->userFromToken($request->input('access_token'));
    //         } else {
    //             $googleUser = Socialite::driver('google')->stateless()->user();
    //         }

    //         $user = User::where('provider', 'google')
    //             ->where('provider_id', $googleUser->getId())
    //             ->first();

    //         if (!$user) {
    //             $user = User::where('email', $googleUser->getEmail())->first();

    //             if ($user) {
    //                 $user->update([
    //                     'provider' => 'google',
    //                     'provider_id' => $googleUser->getId(),
    //                 ]);
    //             } else {
    //                 $user = User::create([
    //                     'name' => $googleUser->getName() ?? $googleUser->getNickname() ?? explode('@', $googleUser->getEmail())[0],
    //                     'email' => $googleUser->getEmail(),
    //                     'provider' => 'google',
    //                     'provider_id' => $googleUser->getId(),
    //                     'user_type' => 'user',
    //                     'password' => null,
    //                 ]);
    //             }
    //         }

    //         Auth::login($user);
    //         $user = $user->fresh();
    //         $token = $user->createToken('auth_token')->plainTextToken;
    //         $user->token = $token;

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Google authentication successful.',
    //             'data' => $user,
    //             'token' => $token,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Google callback authentication failed.',
    //             'error' => $e->getMessage(),
    //         ], 422);
    //     }
    // }

    // #[OA\Get(
    //     path: "/api/apple/redirect",
    //     summary: "Redirect to Apple OAuth Login",
    //     description: "Redirects the client to Apple's authentication page",
    //     operationId: "redirectToApple",
    //     tags: ["Users"],
    //     responses: [
    //         new OA\Response(response: 302, description: "Redirect to Apple OAuth")
    //     ]
    // )]
    // public function redirectToApple()
    // {
    //     return Socialite::driver('apple')->stateless()->redirect();
    // }

    // #[OA\Get(
    //     path: "/api/apple/callback",
    //     summary: "Apple OAuth Callback Redirect URL",
    //     description: "Apple OAuth redirect callback endpoint handling user creation/login",
    //     operationId: "handleAppleCallback",
    //     tags: ["Users"],
    //     responses: [
    //         new OA\Response(response: 200, description: "Apple authentication successful"),
    //         new OA\Response(response: 422, description: "Authentication failed")
    //     ]
    // )]
    // public function handleAppleCallback(Request $request): JsonResponse
    // {
    //     try {
    //         if (!$request->has('code') && !$request->has('access_token')) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Authorization code or access_token is missing from the request. Ensure Apple redirects with ?code= or pass access_token parameter.',
    //             ], 400);
    //         }

    //         if ($request->has('access_token')) {
    //             $appleUser = Socialite::driver('apple')->userFromToken($request->input('access_token'));
    //         } else {
    //             $appleUser = Socialite::driver('apple')->stateless()->user();
    //         }

    //         $user = User::where('provider', 'apple')
    //             ->where('provider_id', $appleUser->getId())
    //             ->first();

    //         if (!$user) {
    //             $user = User::where('email', $appleUser->getEmail())->first();

    //             if ($user) {
    //                 $user->update([
    //                     'provider' => 'apple',
    //                     'provider_id' => $appleUser->getId(),
    //                 ]);
    //             } else {
    //                 $user = User::create([
    //                     'name' => $appleUser->getName() ?? explode('@', $appleUser->getEmail())[0],
    //                     'email' => $appleUser->getEmail(),
    //                     'provider' => 'apple',
    //                     'provider_id' => $appleUser->getId(),
    //                     'user_type' => 'user',
    //                     'password' => null,
    //                 ]);
    //             }
    //         }

    //         Auth::login($user);
    //         $user = $user->fresh();
    //         $token = $user->createToken('auth_token')->plainTextToken;
    //         $user->token = $token;

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Apple authentication successful.',
    //             'data' => $user,
    //             'token' => $token,
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Apple callback authentication failed.',
    //             'error' => $e->getMessage(),
    //         ], 422);
    //     }
    // }

    #[OA\Post(
        path: "/api/users/{id}",
        summary: "Update user details",
        description: "Updates user profile information",
        operationId: "updateUser",
        tags: ["Users"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "User ID", schema: new OA\Schema(type: "integer"))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["name", "email", "user_type", "provider"],
                properties: [
                    new OA\Property(property: "name", type: "string", example: "John Doe"),
                    new OA\Property(property: "email", type: "string", format: "email", example: "john@example.com"),
                    new OA\Property(property: "user_type", type: "string", enum: ["user"], example: "user"),
                    new OA\Property(property: "provider", type: "string", enum: ["google", "apple", "other"], example: "other"),
                    new OA\Property(property: "provider_id", type: "string", nullable: true, example: "123456789"),
                    new OA\Property(property: "password", type: "string", format: "password", nullable: true, example: "secret123"),
                    new OA\Property(property: "password_confirmation", type: "string", format: "password", nullable: true, example: "secret123")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "User updated successfully"),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $user->id,
            'user_type' => 'nullable|in:user,admin',
            'provider' => 'nullable|in:google,apple,other',
            'provider_id' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $userData = array_filter($validated, fn($val) => !is_null($val));

        if (!empty($userData['password'])) {
            $userData['password'] = Hash::make($userData['password']);
        } else {
            unset($userData['password']);
        }

        if (!empty($userData)) {
            $user->update($userData);
        }

        $profileFields = $request->only(['religion_id', 'notification_enabled', 'device_token', 'timezone','date_of_birth','gender']);
        if ($request->has('profile') && is_array($request->profile)) {
            $profileFields = array_merge($profileFields, $request->profile);
        }
        $profileData = array_filter($profileFields, fn($value) => !is_null($value));

        if (!empty($profileData)) {
            UserProfile::updateOrCreate(
                ['user_id' => $user->id],
                $profileData
            );
        }

        $answersToProcess = [];
        if ($request->has('answers') && is_array($request->answers)) {
            $answersToProcess = $request->answers;
        } elseif ($request->has('question_id') && $request->question_id !== null && $request->question_id !== '') {
            $answersToProcess[] = [
                'question_id' => $request->question_id,
                'religion_id' => $request->religion_id,
                'time_slot_id' => $request->time_slot_id,
                'answer' => $request->answer,
            ];
        }

        foreach ($answersToProcess as $ans) {
            if (isset($ans['question_id']) && $ans['question_id'] !== null && $ans['question_id'] !== '') {
                try {
                    $qId = (int) $ans['question_id'];
                    $rId = $ans['religion_id'] ?? $user->profile?->religion_id ?? 1;
                    $tId = $ans['time_slot_id'] ?? null;

                    if (!Question::where('id', $qId)->exists()) {
                        $onboardingStep = OnboardingStep::find($qId);
                        DB::table('questions')->insertOrIgnore([
                            'id' => $qId,
                            'religion_id' => $onboardingStep->religion_id ?? $rId,
                            'time_slot_id' => $tId ?? null,
                            'question' => $onboardingStep->question ?? ('Onboarding Question ' . $qId),
                            'status' => 1
                            
                        ]);
                    }

                    $rawAnswer = $ans['answer'] ?? null;
                    if (is_null($rawAnswer)) {
                        $answerVal = null;
                    } elseif (is_string($rawAnswer)) {
                        $strLower = strtolower(trim($rawAnswer));
                        $answerVal = in_array($strLower, ['yes', 'true', '1'], true) ? 1 : (in_array($strLower, ['no', 'false', '0'], true) ? 0 : (int)$rawAnswer);
                    } else {
                        $answerVal = (int) $rawAnswer;
                    }

                    UserAnswer::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'question_id' => $qId,
                        ],
                        [
                            'religion_id' => $rId,
                            'time_slot_id' => $tId,
                            'answer' => $answerVal,
                            'answered_at' => now(),
                        ]
                    );
                } catch (\Throwable $e) {
                    \Log::error('UserAnswer update error: ' . $e->getMessage());
                }
            }
        }

        $user->load('profile');

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => $user,
        ], 200);
    }

    #[OA\Delete(
        path: "/api/users/{id}",
        summary: "Delete user account",
        description: "Deletes a user by ID",
        operationId: "deleteUser",
        tags: ["Users"],
        parameters: [
            new OA\Parameter(name: "id", in: "path", required: true, description: "User ID", schema: new OA\Schema(type: "integer"))
        ],
        responses: [
            new OA\Response(response: 200, description: "User deleted successfully"),
            new OA\Response(response: 404, description: "User not found")
        ]
    )]
    public function destroy($id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ], 200);
    }
}
