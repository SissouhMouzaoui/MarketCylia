<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Authentifie un utilisateur et crée un token Sanctum.
     */
    public function store(Request $request): JsonResponse
    {
        // Validation des informations de connexion
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Recherche de l'utilisateur à partir de son adresse email
        $user = User::where('email', $credentials['email'])->first();

        // Vérification de l'existence de l'utilisateur et de son mot de passe
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les informations de connexion sont incorrectes.'],
            ]);
        }

        // Création d'un nouveau token d'authentification avec Laravel Sanctum
        $token = $user->createToken('marketcylia-token')->plainTextToken;

        // Retour des informations de l'utilisateur et du token au frontend
        return response()->json([
            'message' => 'Connexion réussie.',
            'user' => $user->load('role'),
            'token' => $token,
        ]);
    }

    /**
     * Déconnecte l'utilisateur en supprimant son token actuel.
     */
    public function destroy(Request $request): JsonResponse
    {
        // Suppression du token utilisé pour la requête actuelle
        $request->user()->currentAccessToken()?->delete();

        return response()->json([
            'message' => 'Déconnexion réussie.',
        ]);
    }
}
