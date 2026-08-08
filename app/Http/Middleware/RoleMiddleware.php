<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Vérifie que l'utilisateur possède le rôle nécessaire
     * pour accéder à la route protégée.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Vérification de l'authentification de l'utilisateur
        if (!$request->user()) {
            return response()->json([
                'message' => 'Utilisateur non authentifié.',
            ], 401);
        }

        // Vérification du rôle de l'utilisateur
        if ($request->user()->role?->name !== $role) {
            return response()->json([
                'message' => 'Accès refusé. Vous n\'avez pas les permissions nécessaires.',
            ], 403);
        }

        // L'utilisateur possède le rôle requis : la requête peut continuer
        return $next($request);
    }
}
