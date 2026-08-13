<?php

use App\Http\Controllers\Admin\AnswersController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\OnboardingStepController;
use App\Http\Controllers\Admin\QuestionsController;
use App\Http\Controllers\Admin\ReligionController;
use App\Http\Controllers\Admin\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate'])->name('login.submit');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/sample', function () {
    return view('sample');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'index'])->name('dashboard');
    Route::get('/religions/index', [ReligionController::class, 'index'])->name('religions.index');
    Route::get('/religions/create', [ReligionController::class, 'create'])->name('religions.create');
    Route::get('/religions/data', [ReligionController::class, 'fetch'])->name('religions.fetch');
    Route::post('/religions/store', [ReligionController::class, 'store'])->name('religions.store');
    Route::get('/religions/{religion}/edit', [ReligionController::class, 'edit'])->name('religions.edit');
    Route::post('/religions/{religion}/update', [ReligionController::class, 'update'])->name('religions.update');
    Route::get('/religions/{religion}/image', [ReligionController::class, 'image'])->name('religions.image');
    Route::delete('/religions/{religion}', [ReligionController::class, 'destroy'])->name('religions.destroy');

    Route::get('/users/index', [UsersController::class, 'index'])->name('users.index');

    Route::get('/answers/index', [AnswersController::class, 'index'])->name('answers.index');

    Route::get('/questions/index', [QuestionsController::class, 'index'])->name('questions.index');
    Route::get('/questions/create', [QuestionsController::class, 'create'])->name('questions.create');
    Route::get('/questions/data', [QuestionsController::class, 'fetch'])->name('questions.fetch');
    Route::post('/questions/store', [QuestionsController::class, 'store'])->name('questions.store');
    Route::get('/questions/{question}/edit', [QuestionsController::class, 'edit'])->name('questions.edit');
    Route::post('/questions/{question}/update', [QuestionsController::class, 'update'])->name('questions.update');
    Route::delete('/questions/{question}', [QuestionsController::class, 'destroy'])->name('questions.destroy');

    Route::get('/onboarding-steps/index', [OnboardingStepController::class, 'index'])->name('onboarding-steps.index');
    Route::get('/onboarding-steps/create', [OnboardingStepController::class, 'create'])->name('onboarding-steps.create');
    Route::get('/onboarding-steps/data', [OnboardingStepController::class, 'fetch'])->name('onboarding-steps.fetch');
    Route::post('/onboarding-steps/store', [OnboardingStepController::class, 'store'])->name('onboarding-steps.store');
    Route::get('/onboarding-steps/{onboarding_step}/edit', [OnboardingStepController::class, 'edit'])->name('onboarding-steps.edit');
    Route::put('/onboarding-steps/{onboarding_step}/update', [OnboardingStepController::class, 'update'])->name('onboarding-steps.update');
    Route::delete('/onboarding-steps/{onboarding_step}', [OnboardingStepController::class, 'destroy'])->name('onboarding-steps.destroy');
});
