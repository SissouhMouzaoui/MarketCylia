<?php

use App\Http\Controllers\Api\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\Auth\RegisteredUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route d'inscription des nouveaux utilisateurs
Route::post('/register', [RegisteredUserController::class, 'store']);

// Route de connexion des utilisateurs
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// Route de déconnexion des utilisateurs authentifiés
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum');

// Route permettant de récupérer l'utilisateur actuellement authentifié
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user()->load('role');
});
