<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ReligionController;


Route::get('/', [AuthController::class, 'login'])->name('login');

Route::get('/religions/index', [ReligionController::class, 'index'])->name('religions.index');
Route::get('/religions/create', [ReligionController::class, 'create'])->name('religions.create');
Route::get('/religions/data', [ReligionController::class, 'fetch'])->name('religions.fetch');
Route::post('/religions/store', [ReligionController::class, 'store'])->name('religions.store');
Route::get('/religions/{religion}/edit', [ReligionController::class, 'edit'])->name('religions.edit');
Route::post('/religions/{religion}/update', [ReligionController::class, 'update'])->name('religions.update');
Route::get('/religions/{religion}/image', [ReligionController::class, 'image'])->name('religions.image');
Route::delete('/religions/{religion}', [ReligionController::class, 'destroy'])->name('religions.destroy');