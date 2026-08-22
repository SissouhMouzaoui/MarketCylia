<?php

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| PAGE D'ACCUEIL
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    return view('home');

});


/*
|--------------------------------------------------------------------------
| AUTHENTIFICATION
|--------------------------------------------------------------------------
*/

/*
| Page de connexion
*/

Route::get('/login', function () {

    return view('auth.login');

});

/*
| Page de création de compte
*/

Route::get('/register', function () {

    return view('auth.register');

});


/*
|--------------------------------------------------------------------------
| ESPACE VENDEUR
|--------------------------------------------------------------------------
*/

Route::get('/seller/dashboard', function () {

    return view('seller.dashboard');

});


/*
|--------------------------------------------------------------------------
| PRODUITS VENDEUR
|--------------------------------------------------------------------------
*/

/*
| Création d'un produit
*/

Route::get('/seller/products/create', function () {

    return view('seller.products.create');

});


/*
| Liste des produits du vendeur
*/

Route::get('/seller/products', function () {

    return view('seller.products.index');

});


/*
| Modification d'un produit
*/

Route::get('/seller/products/{id}/edit', function ($id) {

    return view('seller.products.edit', [

        'productId' => $id,

    ]);

});


/*
| Détails d'un produit vendeur
*/

Route::get('/seller/products/{product}', function ($product) {

    return view('seller.products.show', [

        'productId' => $product,

    ]);

});


/*
|--------------------------------------------------------------------------
| PRODUITS PUBLICS
|--------------------------------------------------------------------------
*/

/*
| Liste des produits
*/

Route::get('/products', function () {

    return view('products.index');

});


/*
| Détails d'un produit public
*/

Route::get('/products/{product}', function ($product) {

    return view('products.show', [

        'productId' => $product

    ]);

});
/*
|--------------------------------------------------------------------------
| Panier
|--------------------------------------------------------------------------
*/

Route::get('/cart', function () {

    return view('cart');

});
Route::get(
    '/orders',
    function () {
        return view('orders.index');
    }
);
Route::get(
    '/orders/{id}',
    function ($id) {
        return view(
            'orders.show',
            [
                'orderId' => $id
            ]
        );
    }
);
Route::get(
    '/seller/orders',
    function () {
        return view('seller.orders.index');
    }
);
/*
|--------------------------------------------------------------------------
| COMMANDES CLIENT
|--------------------------------------------------------------------------
*/

Route::get('/orders', function () {

    return view('orders.index');

});

Route::get('/orders/{order}', function ($order) {

    return view('orders.show', [
        'orderId' => $order,
    ]);

});


/*
|--------------------------------------------------------------------------
| COMMANDES VENDEUR
|--------------------------------------------------------------------------
*/

Route::get('/seller/orders', function () {

    return view('seller.orders.index');

});

Route::get('/seller/orders/{order}', function ($order) {

    return view('seller.orders.show', [
        'orderId' => $order,
    ]);

});
