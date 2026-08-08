<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Enregistre un nouvel utilisateur dans l'application.
     */
    public function store(Request $request): JsonResponse
    {
        // Validation des informations envoyées par l'utilisateur
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class,
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ]);

        // Attribution automatique du rôle Customer aux nouveaux utilisateurs
        $customerRole = Role::where('name', 'Customer')->firstOrFail();

        // Création du compte utilisateur
        // Le mot de passe sera automatiquement hashé grâce au cast "hashed" du modèle User
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role_id' => $customerRole->id,
        ]);

        // Création d'un token d'authentification avec Laravel Sanctum
        $token = $user->createToken('marketcylia-token')->plainTextToken;

        // Connexion de l'utilisateur après son inscription
        Auth::login($user);

        // Retour d'une réponse JSON contenant les informations nécessaires au frontend
        return response()->json([
            'message' => 'Inscription réussie.',
            'user' => $user->load('role'),
            'token' => $token,
        ], 201);
    }
}
