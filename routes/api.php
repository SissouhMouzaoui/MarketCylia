<?php

use App\Http\Controllers\Api\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\Auth\RegisteredUserController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| PRODUITS PUBLICS
|--------------------------------------------------------------------------
*/

Route::get(
    '/products',
    [ProductController::class, 'publicIndex']
);

Route::get(
    '/products/{product}',
    [ProductController::class, 'publicShow']
);


/*
|--------------------------------------------------------------------------
| AUTHENTIFICATION
|--------------------------------------------------------------------------
*/

Route::post(
    '/register',
    [RegisteredUserController::class, 'store']
);

Route::post(
    '/login',
    [AuthenticatedSessionController::class, 'store']
);

Route::post(
    '/logout',
    [AuthenticatedSessionController::class, 'destroy']
)->middleware('auth:sanctum');


/*
|--------------------------------------------------------------------------
| UTILISATEUR CONNECTÉ
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get(
    '/user',
    function (Request $request) {

        return response()->json(
            $request->user()->load('role')
        );

    }
);


/*
|--------------------------------------------------------------------------
| ADMIN
|--------------------------------------------------------------------------
*/

Route::get(
    '/admin/test',
    function () {

        return response()->json([
            'message' =>
                'Bienvenue Admin !',
        ]);

    }
)->middleware([
    'auth:sanctum',
    'role:Admin',
]);


/*
|--------------------------------------------------------------------------
| CATÉGORIES
|--------------------------------------------------------------------------
*/

Route::get(
    '/categories',
    function () {

        return response()->json(
            Category::where(
                'is_active',
                true
            )
            ->orderBy('name')
            ->get()
        );

    }
);


/*
|--------------------------------------------------------------------------
| PRODUITS DU VENDEUR
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth:sanctum',
    'role:Seller',
])
->prefix('seller')
->group(function () {


    /*
    ----------------------------------------------------------
    Produits
    ----------------------------------------------------------
    */

    Route::get(
        '/products',
        [ProductController::class, 'index']
    );


    Route::post(
        '/products',
        [ProductController::class, 'store']
    );


    Route::get(
        '/products/{product}',
        [ProductController::class, 'show']
    );


    Route::put(
        '/products/{product}',
        [ProductController::class, 'update']
    );


    Route::delete(
        '/products/{product}',
        [ProductController::class, 'destroy']
    );


    /*
    ----------------------------------------------------------
    Commandes du vendeur
    ----------------------------------------------------------
    */

    Route::get(
        '/orders',
        [OrderController::class, 'sellerIndex']
    );


    /*
    ----------------------------------------------------------
    Détail d'une commande pour le vendeur
    ----------------------------------------------------------
    */

    Route::get(
        '/orders/{order}',
        [OrderController::class, 'sellerShow']
    );


    /*
    ----------------------------------------------------------
    Modifier le statut
    ----------------------------------------------------------
    */

    Route::put(
        '/orders/{order}/status',
        [OrderController::class, 'sellerUpdateStatus']
    );

});


/*
|--------------------------------------------------------------------------
| IMAGES DES PRODUITS
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->delete(
    '/seller/products/{product}/images/{image}',
    [ProductController::class, 'destroyImage']
);

Route::middleware('auth:sanctum')->put(
    '/seller/products/{product}/images/{image}/primary',
    [ProductController::class, 'setPrimaryImage']
);

Route::middleware('auth:sanctum')->post(
    '/seller/products/{product}/images',
    [ProductController::class, 'storeImages']
);


/*
|--------------------------------------------------------------------------
| PANIER
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')
    ->prefix('cart')
    ->group(function () {

        Route::get(
            '/',
            [CartController::class, 'index']
        );

        Route::post(
            '/',
            [CartController::class, 'store']
        );

        Route::put(
            '/{cartItem}',
            [CartController::class, 'update']
        );

        Route::delete(
            '/{cartItem}',
            [CartController::class, 'destroy']
        );

    });


/*
|--------------------------------------------------------------------------
| COMMANDES CLIENT
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')
    ->group(function () {


        /*
        ----------------------------------------------------------
        Mes commandes
        ----------------------------------------------------------
        */

        Route::get(
            '/orders',
            [OrderController::class, 'index']
        );


        /*
        ----------------------------------------------------------
        Créer une commande
        ----------------------------------------------------------
        */

        Route::post(
            '/orders',
            [OrderController::class, 'store']
        );


        /*
        ----------------------------------------------------------
        Détails commande client
        ----------------------------------------------------------
        */

        Route::get(
            '/orders/{order}',
            [OrderController::class, 'show']
        );


        /*
        ----------------------------------------------------------
        Annuler commande
        ----------------------------------------------------------
        */

        Route::put(
            '/orders/{order}/cancel',
            [OrderController::class, 'cancel']
        );

    });
