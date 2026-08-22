@extends('layouts.app')

@section('title', 'Produits - MarketCylia')

@section('content')

<div class="container">

    {{-- ==========================================================
         EN-TÊTE
    ========================================================== --}}

    <div class="mb-4">

        <h1 class="fw-bold">
            Nos produits
        </h1>

        <p class="text-muted">
            Découvrez les produits disponibles sur MarketCylia.
        </p>

    </div>


    {{-- ==========================================================
         ERREUR
    ========================================================== --}}

    <div
        id="productsError"
        class="alert alert-danger d-none">
    </div>


    {{-- ==========================================================
         CHARGEMENT
    ========================================================== --}}

    <div
        id="productsLoading"
        class="text-center py-5">

        <div class="spinner-border"></div>

        <p class="text-muted mt-3">
            Chargement des produits...
        </p>

    </div>


    {{-- ==========================================================
         PRODUITS
    ========================================================== --}}

    <div
        id="productsContainer"
        class="row g-4">
    </div>


    {{-- ==========================================================
         AUCUN PRODUIT
    ========================================================== --}}

    <div
        id="emptyProducts"
        class="d-none">

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center py-5">

                <div class="display-4">
                    🛍️
                </div>

                <h4 class="mt-3">
                    Aucun produit disponible
                </h4>

                <p class="text-muted mb-0">
                    Aucun produit n'est actuellement disponible.
                </p>

            </div>

        </div>

    </div>

</div>


<script>

    /*
    ==========================================================
    PRODUITS PUBLICS
    ==========================================================
    */


    const productsContainer =
        document.getElementById(
            'productsContainer'
        );


    const productsLoading =
        document.getElementById(
            'productsLoading'
        );


    const productsError =
        document.getElementById(
            'productsError'
        );


    const emptyProducts =
        document.getElementById(
            'emptyProducts'
        );


    /*
    ==========================================================
    AFFICHER UNE ERREUR
    ==========================================================
    */

    function showProductsError(message) {

        productsError.textContent =
            message ||
            'Une erreur est survenue.';

        productsError.classList.remove(
            'd-none'
        );

    }


    /*
    ==========================================================
    CHARGER LES PRODUITS
    ==========================================================
    */

    async function loadPublicProducts() {

        try {

            /*
            --------------------------------------------------
            Appel API
            --------------------------------------------------
            */

            const response =
                await fetch(
                    'http://127.0.0.1:8000/api/products',
                    {
                        method: 'GET',

                        headers: {
                            'Accept':
                                'application/json'
                        }
                    }
                );


            const data =
                await response.json();


            /*
            --------------------------------------------------
            Vérifier la réponse
            --------------------------------------------------
            */

            if (!response.ok) {

                throw new Error(
                    data.message ||
                    'Impossible de charger les produits.'
                );

            }


            /*
            --------------------------------------------------
            Récupérer les produits
            --------------------------------------------------
            */

            const products =
                data.products || [];


            /*
            --------------------------------------------------
            Masquer le chargement
            --------------------------------------------------
            */

            productsLoading.classList.add(
                'd-none'
            );


            /*
            --------------------------------------------------
            Aucun produit
            --------------------------------------------------
            */

            if (products.length === 0) {

                emptyProducts.classList.remove(
                    'd-none'
                );

                return;

            }


            /*
            --------------------------------------------------
            Afficher les produits
            --------------------------------------------------
            */

            productsContainer.innerHTML =
                products.map(
                    function(product) {

                        /*
                        ------------------------------------------
                        Images
                        ------------------------------------------
                        */

                        const images =
                            product.images || [];


                        const primaryImage =
                            images.find(
                                function(image) {
                                    return image.is_primary;
                                }
                            ) ||
                            images[0];


                        /*
                        ------------------------------------------
                        Image HTML
                        ------------------------------------------
                        */

                        let imageHtml;


                        if (primaryImage) {

                            imageHtml = `

                                <img
                                    src="/storage/${primaryImage.image}"
                                    alt="${product.name}"
                                    class="card-img-top"
                                    style="
                                        height: 250px;
                                        object-fit: cover;
                                    "
                                >

                            `;

                        } else {

                            imageHtml = `

                                <div
                                    class="d-flex align-items-center justify-content-center bg-light"
                                    style="
                                        height: 250px;
                                    "
                                >

                                    <span
                                        class="display-5">
                                        📷
                                    </span>

                                </div>

                            `;

                        }


                        /*
                        ------------------------------------------
                        Stock
                        ------------------------------------------
                        */

                        let stockHtml;


                        if (product.stock > 0) {

                            stockHtml = `

                                <span class="badge text-bg-success">
                                    Disponible
                                </span>

                            `;

                        } else {

                            stockHtml = `

                                <span class="badge text-bg-secondary">
                                    Rupture de stock
                                </span>

                            `;

                        }


                        /*
                        ------------------------------------------
                        Carte produit
                        ------------------------------------------
                        */

                        return `

                            <div class="col-12 col-sm-6 col-lg-4 col-xl-3">

                                <div
                                    class="card h-100 border-0 shadow-sm"
                                    style="
                                        overflow: hidden;
                                        transition: transform .2s;
                                    "
                                >

                                    ${imageHtml}


                                    <div class="card-body d-flex flex-column">

                                        <h5 class="fw-semibold mb-2">

                                            ${product.name}

                                        </h5>


                                        <p
                                            class="text-muted small mb-3"
                                            style="
                                                min-height: 42px;
                                            "
                                        >

                                            ${product.description ?? ''}

                                        </p>


                                        <div
                                            class="d-flex justify-content-between align-items-center mb-3"
                                        >

                                            <strong
                                                class="fs-5">

                                                ${product.price} DA

                                            </strong>


                                            ${stockHtml}

                                        </div>


                                        <a
                                            href="/products/${product.id}"
                                            class="btn btn-dark w-100 mt-auto"
                                        >

                                            Voir le produit

                                        </a>

                                    </div>

                                </div>

                            </div>

                        `;

                    }
                ).join('');


        } catch (error) {

            console.error(
                'Erreur produits publics :',
                error
            );


            productsLoading.classList.add(
                'd-none'
            );


            showProductsError(
                error.message
            );

        }

    }


    /*
    ==========================================================
    DÉMARRAGE
    ==========================================================
    */

    loadPublicProducts();

</script>

@endsection
