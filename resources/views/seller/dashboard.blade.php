@extends('layouts.app')

@section('title', 'Seller Dashboard - MarketCylia')

@section('content')

<div class="row">

    {{-- =========================
         SIDEBAR
    ========================== --}}
    <div class="col-lg-3 mb-4">

        <div class="card border-0 shadow-sm">

            <div class="card-body p-3">

                <div class="text-center py-3">

                    <div class="fs-1">
                        👤
                    </div>

                    <h5 class="fw-bold mb-1">
                        Espace vendeur
                    </h5>

                    <small class="text-muted">
                        MarketCylia
                    </small>

                </div>

                <hr>

                <div class="list-group list-group-flush">

                    <a
                        href="/seller/dashboard"
                        class="list-group-item list-group-item-action active">
                        📊 Dashboard
                    </a>

                    <a
                        href="/seller/products"
                        class="list-group-item list-group-item-action">
                        📦 Mes produits
                    </a>

                    <a
                        href="#"
                        class="list-group-item list-group-item-action">
                        🛒 Mes commandes
                    </a>

                    <a
                        href="#"
                        class="list-group-item list-group-item-action">
                        🏪 Ma boutique
                    </a>

                    <a
                        href="#"
                        class="list-group-item list-group-item-action">
                        ⚙️ Paramètres
                    </a>

                </div>

                <hr>

                <button
                    type="button"
                    class="btn btn-outline-danger w-100"
                    id="logoutButton">

                    🚪 Déconnexion

                </button>

            </div>

        </div>

    </div>


    {{-- =========================
         MAIN CONTENT
    ========================== --}}
    <div class="col-lg-9">

        <div class="mb-4">

            <h1 class="fw-bold">
                Seller Dashboard
            </h1>

            <p class="text-muted">
                Bienvenue dans votre espace vendeur.
            </p>

        </div>


        {{-- =========================
             STATISTICS
        ========================== --}}
        <div class="row g-4 mb-4">

            <div class="col-md-4">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <p class="text-muted mb-1">
                                    Produits
                                </p>

                                <h2
                                    class="fw-bold mb-0"
                                    id="productsCount">

                                    0

                                </h2>

                            </div>

                            <div class="fs-1">
                                📦
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <p class="text-muted mb-1">
                                    Commandes
                                </p>

                                <h2 class="fw-bold mb-0">
                                    0
                                </h2>

                            </div>

                            <div class="fs-1">
                                🛒
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <div class="col-md-4">

                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between">

                            <div>

                                <p class="text-muted mb-1">
                                    Ventes
                                </p>

                                <h2 class="fw-bold mb-0">
                                    0 DA
                                </h2>

                            </div>

                            <div class="fs-1">
                                💰
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =========================
             PRODUCTS
        ========================== --}}
        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>

                        <h4 class="fw-bold mb-1">
                            Mes produits
                        </h4>

                        <p class="text-muted mb-0">
                            Produits ajoutés à votre boutique.
                        </p>

                    </div>
                    <a
                     href="/seller/products/create"
                     class="btn btn-dark">

                      + Ajouter un produit

                   </a>

                </div>


                <div id="productsContainer">

                    <div class="text-center py-5">

                        <div class="spinner-border"></div>

                        <p class="text-muted mt-3">
                            Chargement des produits...
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<script>

    /*
    ==========================================================
    SELLER DASHBOARD
    ==========================================================
    */

    const token = localStorage.getItem('marketcylia_token');

    const productsContainer =
        document.getElementById('productsContainer');

    const productsCount =
        document.getElementById('productsCount');


    /*
    ==========================================================
    VÉRIFICATION DU TOKEN
    ==========================================================
    */

    if (!token) {

        window.location.href = '/login';

    }


    /*
    ==========================================================
    CHARGEMENT DES PRODUITS
    ==========================================================
    */

    async function loadProducts() {

        try {

            const response = await fetch(
                'http://127.0.0.1:8000/api/seller/products',
                {
                    method: 'GET',

                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    }
                }
            );


            const data = await response.json();


            if (!response.ok) {

                throw new Error(
                    data.message ||
                    'Impossible de charger les produits.'
                );

            }


            const products = data.products || [];


            /*
            --------------------------------------------------
            Nombre de produits
            --------------------------------------------------
            */

            productsCount.textContent =
                products.length;


            /*
            --------------------------------------------------
            Aucun produit
            --------------------------------------------------
            */

            if (products.length === 0) {

                productsContainer.innerHTML = `

                    <div class="text-center py-5">

                        <div class="fs-1">
                            📦
                        </div>

                        <h5 class="mt-3">
                            Aucun produit
                        </h5>

                        <p class="text-muted">
                            Vous n'avez pas encore ajouté de produit.
                        </p>

                    </div>

                `;

                return;

            }


            /*
            --------------------------------------------------
            Affichage des produits
            --------------------------------------------------
            */

            productsContainer.innerHTML = '';


            products.forEach(function(product) {

                productsContainer.innerHTML += `

                    <div class="border rounded p-3 mb-3">

                        <div class="row align-items-center">

                            <div class="col-md-7">

                                <h5 class="fw-bold mb-1">
                                    ${product.name}
                                </h5>

                                <p class="text-muted mb-1">
                                    ${product.description ?? ''}
                                </p>

                                <small class="text-muted">
                                    Stock : ${product.stock}
                                </small>

                            </div>


                            <div class="col-md-3">

                                <strong>
                                    ${product.price} DA
                                </strong>

                            </div>


                            <div class="col-md-2 text-md-end mt-2 mt-md-0">

                                <button
                                    class="btn btn-sm btn-outline-dark">

                                    Modifier

                                </button>

                            </div>

                        </div>

                    </div>

                `;

            });


        } catch (error) {

            console.error(
                'Erreur Dashboard :',
                error
            );


            productsContainer.innerHTML = `

                <div class="alert alert-danger">

                    Impossible de charger les produits.

                </div>

            `;

        }

    }


    /*
    ==========================================================
    DÉMARRAGE
    ==========================================================
    */

    loadProducts();

</script>

@endsection
