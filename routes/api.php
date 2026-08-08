<?php

use App\Http\Controllers\Api\Auth\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route d'inscription des nouveaux utilisateurs
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
