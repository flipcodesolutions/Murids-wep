<?php
use App\Http\Controllers\Api\UsersController;
use Illuminate\Support\Facades\Route;

Route::get('/users', [UsersController::class, 'index']);
Route::get('/users/{id}', [UsersController::class, 'show']);
Route::post('/register', [UsersController::class, 'register']);
Route::post('/users/{id}', [UsersController::class, 'update']); // or Route::put(...)
Route::delete('/users/{id}', [UsersController::class, 'destroy']);
