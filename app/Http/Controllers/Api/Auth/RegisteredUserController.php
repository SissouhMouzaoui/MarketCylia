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
     * Enregistre un nouvel utilisateur.
     */
    public function store(Request $request): JsonResponse
    {
        /*
        ==========================================================
        VALIDATION
        ==========================================================
        */

        $validated = $request->validate([

            'name' => [
                'required',
                'string',
                'max:255'
            ],

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

            /*
            ------------------------------------------------------
            Type de compte
            ------------------------------------------------------

            customer = acheteur
            seller   = vendeur

            Admin n'est jamais accepté ici.
            ------------------------------------------------------
            */

            'account_type' => [
                'required',
                'in:customer,seller'
            ],

        ]);


        /*
        ==========================================================
        DÉTERMINER LE RÔLE
        ==========================================================
        */

        if ($validated['account_type'] === 'seller') {

            $roleName = 'Seller';

        } else {

            $roleName = 'Customer';

        }


        /*
        ==========================================================
        RÉCUPÉRER LE RÔLE
        ==========================================================
        */

        $role = Role::where(
            'name',
            $roleName
        )->firstOrFail();


        /*
        ==========================================================
        CRÉER L'UTILISATEUR
        ==========================================================
        */

        $user = User::create([

            'name' => $validated['name'],

            'email' => $validated['email'],

            'password' => $validated['password'],

            'role_id' => $role->id,

        ]);


        /*
        ==========================================================
        CRÉER LE TOKEN
        ==========================================================
        */

        $token =
            $user
                ->createToken('marketcylia-token')
                ->plainTextToken;


        /*
        ==========================================================
        CONNECTER L'UTILISATEUR
        ==========================================================
        */

        Auth::login($user);


        /*
        ==========================================================
        RÉPONSE
        ==========================================================
        */

        return response()->json([

            'message' =>
                'Inscription réussie.',

            'user' =>
                $user->load('role'),

            'token' =>
                $token,

        ], 201);
    }
}
