<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): Response
    {
        // Validation des informations saisies par le nouvel utilisateur
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class
            ],
            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults()
            ],
        ]);

        // Récupération du rôle Customer pour les nouveaux utilisateurs
        $customerRole = Role::where('name', 'Customer')->firstOrFail();

        // Création du compte utilisateur avec le rôle Customer
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->string('password')),
            'role_id' => $customerRole->id,
        ]);

        // Déclenchement de l'événement d'inscription
        event(new Registered($user));

        // Connexion automatique de l'utilisateur après son inscription
        Auth::login($user);

        return response()->noContent();
    }
}
