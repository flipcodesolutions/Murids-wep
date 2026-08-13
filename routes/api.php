<?php

use App\Http\Controllers\Api\OnboardingStepController;
use App\Http\Controllers\Api\UsersController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [UsersController::class, 'login']);
Route::get('/google/redirect', [UsersController::class, 'redirectToGoogle']);
Route::get('/google/callback', [UsersController::class, 'handleGoogleCallback']);
Route::get('/apple/redirect', [UsersController::class, 'redirectToApple']);
Route::get('/apple/callback', [UsersController::class, 'handleAppleCallback']);
Route::get('/users', [UsersController::class, 'index']);
Route::get('/users/{id}', [UsersController::class, 'show']);
Route::post('/register', [UsersController::class, 'register']);
Route::post('/users/{id}', [UsersController::class, 'update']);
Route::delete('/users/{id}', [UsersController::class, 'destroy']);

Route::get('/onboarding-steps', [OnboardingStepController::class, 'index']);