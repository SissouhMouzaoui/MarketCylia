@extends('layouts.app')

@section('title', 'Détails du produit - MarketCylia')

@section('content')

<div class="container">

{{-- ==========================================================
     RETOUR
========================================================== --}}

<div class="mb-4">

    <a
        href="/products"
        class="btn btn-outline-secondary">

        ← Retour aux produits

    </a>

</div>


{{-- ==========================================================
     ERREUR
========================================================== --}}

<div
    id="productError"
    class="alert alert-danger d-none">
</div>


{{-- ==========================================================
     CHARGEMENT
========================================================== --}}

<div
    id="loading"
    class="text-center py-5">

    <div class="spinner-border"></div>

    <p class="text-muted mt-3">
        Chargement du produit...
    </p>

</div>


{{-- ==========================================================
     PRODUIT
========================================================== --}}

<div
    id="productContainer"
    class="d-none">

    <div class="row g-4 g-lg-5">

        {{-- ==================================================
             GALERIE
        ================================================== --}}

        <div class="col-lg-7">

            <div class="card border-0 shadow-sm">

                <div class="card-body p-3 p-md-4">

                    {{-- IMAGE PRINCIPALE --}}

                    <div
                        class="mb-4 position-relative"
                        style="
                            height: 500px;
                            background: #f8f9fa;
                            border-radius: 16px;
                            overflow: hidden;
                        "
                    >

                        <img
                            id="mainImage"
                            src=""
                            alt=""
                            style="
                                width: 100%;
                                height: 100%;
                                object-fit: contain;
                            "
                        >

                    </div>


                    {{-- MINIATURES --}}

                    <div
                        id="thumbnails"
                        class="row g-2">
                    </div>

                </div>

            </div>

        </div>


        {{-- ==================================================
             INFORMATIONS
        ================================================== --}}

        <div class="col-lg-5">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body p-4 p-md-5">

                    {{-- NOM --}}

                    <h1
    id="productName"
    class="fw-bold mb-2">
</h1>

<div
    id="productCategory"
    class="mb-4">
</div>


                    {{-- ==================================================
                         CATÉGORIE
                    ================================================== --}}

                    <div
                        id="productCategory"
                        class="mb-3">
                    </div>


                    {{-- PRIX --}}

                    <div class="mb-4">

                        <span
                            id="productPrice"
                            class="fs-2 fw-bold">
                        </span>

                    </div>


                    {{-- STOCK --}}

                    <div
                        id="productStock"
                        class="mb-4">
                    </div>


                    <hr>


                    {{-- ==================================================
                         DESCRIPTION
                    ================================================== --}}

                    <h5 class="fw-semibold mt-4 mb-3">

                        Description

                    </h5>


                    <p
                        id="productDescription"
                        class="text-muted"
                        style="white-space: pre-line;">
                    </p>


                    <hr class="my-4">


                    {{-- ==================================================
                         INFORMATIONS VENDEUR
                    ================================================== --}}

                    <h5 class="fw-semibold mb-3">

                        Vendeur

                    </h5>


                    <div
                        id="sellerInfo"
                        class="bg-light rounded-3 p-3 mb-4">

                        <div class="d-flex align-items-center">

                            <div
                                class="d-flex align-items-center justify-content-center bg-white rounded-circle me-3"
                                style="
                                    width: 48px;
                                    height: 48px;
                                "
                            >

                                👤

                            </div>


                            <div>

                                <div
                                    id="sellerName"
                                    class="fw-semibold">

                                    Vendeur

                                </div>

                                <small class="text-muted">
                                    Vendeur MarketCylia
                                </small>

                            </div>

                        </div>

                    </div>


                    {{-- ==================================================
                         ACTIONS
                    ================================================== --}}

                    <div class="mt-4">

                        <button
                            type="button"
                            id="cartButton"
                            class="btn btn-dark btn-lg w-100">

                            🛒 Ajouter au panier

                        </button>


                        <a
                            href="/login"
                            id="loginButton"
                            class="btn btn-outline-dark btn-lg w-100 mt-2 d-none">

                            🔐 Se connecter pour continuer

                        </a>


                        <div
                            id="loginMessage"
                            class="text-muted small text-center mt-3 d-none">

                            Vous devez être connecté pour ajouter
                            ce produit à votre panier.

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

</div>

<script>

    /*
    ==========================================================
    DÉTAILS DU PRODUIT PUBLIC
    ==========================================================
    */

    const productId =
        {{ $productId }};


    /*
    ==========================================================
    ÉLÉMENTS DOM
    ==========================================================
    */

    const loading =
        document.getElementById('loading');

    const productContainer =
        document.getElementById('productContainer');

    const productError =
        document.getElementById('productError');

    const mainImage =
        document.getElementById('mainImage');

    const thumbnails =
        document.getElementById('thumbnails');

    const productName =
        document.getElementById('productName');

    const productCategory =
        document.getElementById('productCategory');

    const productPrice =
        document.getElementById('productPrice');

    const productDescription =
        document.getElementById('productDescription');

    const productStock =
        document.getElementById('productStock');

    const sellerName =
        document.getElementById('sellerName');

    const cartButton =
        document.getElementById('cartButton');

    const loginButton =
        document.getElementById('loginButton');

    const loginMessage =
        document.getElementById('loginMessage');


    /*
    ==========================================================
    VÉRIFIER LA CONNEXION
    ==========================================================
    */

    const token =
        localStorage.getItem('marketcylia_token');


    /*
    ==========================================================
    CHARGER LE PRODUIT
    ==========================================================
    */

    async function loadProduct() {

        try {

            const response =
                await fetch(
                    `http://127.0.0.1:8000/api/products/${productId}`,
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


            if (!response.ok) {

                throw new Error(
                    data.message ||
                    'Impossible de charger le produit.'
                );

            }


            const product =
                data.product;


            /*
            ==================================================
            INFORMATIONS PRODUIT
            ==================================================
            */

            productName.textContent =
                product.name;

            productPrice.textContent =
                `${product.price} DA`;


            productDescription.textContent =
                product.description ||
                'Aucune description disponible.';


            /*
            ==================================================
            CATÉGORIE DU PRODUIT
            ==================================================
            */

            if (
                product.category &&
                product.category.name
            ) {

                productCategory.innerHTML = `

                    <span class="badge rounded-pill text-bg-light border">

                        🏷️ ${product.category.name}

                    </span>

                `;

            } else {

                productCategory.innerHTML = `

                    <span class="text-muted small">

                        Catégorie non définie

                    </span>

                `;

            }


            /*
            ==================================================
            INFORMATIONS VENDEUR
            ==================================================
            */

            if (product.user) {

    sellerName.textContent =
        product.user.full_name ||
        product.user.name ||
        'Vendeur';

} else {

    sellerName.textContent =
        'Vendeur MarketCylia';

}


            /*
            ==================================================
            STOCK
            ==================================================
            */

            if (product.stock > 0) {

                productStock.innerHTML = `

                    <span
                        class="badge text-bg-success fs-6">

                        Disponible

                    </span>


                    <div
                        class="text-muted small mt-2">

                        ${product.stock}
                        article(s) disponible(s)

                    </div>

                `;

            } else {

                productStock.innerHTML = `

                    <span
                        class="badge text-bg-secondary fs-6">

                        Rupture de stock

                    </span>

                `;

            }


            /*
            ==================================================
            IMAGES
            ==================================================
            */

            const images =
                product.images || [];


            if (images.length === 0) {

                mainImage.style.display =
                    'none';


                mainImage.parentElement.innerHTML = `

                    <div
                        class="d-flex align-items-center justify-content-center h-100">

                        <span class="display-3">
                            📷
                        </span>

                    </div>

                `;


                thumbnails.innerHTML = '';

            } else {

                /*
                --------------------------------------------------
                IMAGE PRINCIPALE
                --------------------------------------------------
                */

                const primaryImage =
                    images.find(
                        function(image) {

                            return image.is_primary;

                        }
                    ) ||
                    images[0];


                changeMainImage(
                    primaryImage.image,
                    product.name
                );


                /*
                --------------------------------------------------
                MINIATURES
                --------------------------------------------------
                */

                thumbnails.innerHTML =
                    images.map(
                        function(image) {

                            const isPrimary =
                                image.id ===
                                primaryImage.id;


                            return `

                                <div
                                    class="col-4 col-sm-3 col-md-2">

                                    <button
                                        type="button"
                                        class="btn p-1 w-100 border rounded-3 ${
                                            isPrimary
                                            ? 'border-dark border-2'
                                            : ''
                                        }"
                                        onclick="changeMainImage(
                                            '${image.image}',
                                            '${product.name}'
                                        )"
                                    >

                                        <img
                                            src="/storage/${image.image}"
                                            alt="${product.name}"
                                            class="img-fluid rounded-2"
                                            style="
                                                height: 80px;
                                                width: 100%;
                                                object-fit: cover;
                                            "
                                        >

                                    </button>

                                </div>

                            `;

                        }
                    ).join('');

            }


            /*
            ==================================================
            PANIER
            ==================================================
            */

            if (!token) {

                cartButton.classList.add(
                    'd-none'
                );

                loginButton.classList.remove(
                    'd-none'
                );

                loginMessage.classList.remove(
                    'd-none'
                );

            } else {

                cartButton.classList.remove(
                    'd-none'
                );

                loginButton.classList.add(
                    'd-none'
                );

                loginMessage.classList.add(
                    'd-none'
                );

            }


            /*
            ==================================================
            AFFICHER LA PAGE
            ==================================================
            */

            loading.classList.add(
                'd-none'
            );

            productContainer.classList.remove(
                'd-none'
            );


        } catch (error) {

            console.error(
                'Erreur produit :',
                error
            );


            loading.classList.add(
                'd-none'
            );


            productError.textContent =
                error.message ||
                'Une erreur est survenue.';


            productError.classList.remove(
                'd-none'
            );

        }

    }


    /*
    ==========================================================
    CHANGER L'IMAGE PRINCIPALE
    ==========================================================
    */

    function changeMainImage(
        imagePath,
        productName
    ) {

        mainImage.src =
            `/storage/${imagePath}`;

        mainImage.alt =
            productName;

        mainImage.style.display =
            'block';

    }


    /*
    ==========================================================
    AJOUTER AU PANIER
    ==========================================================
    */

    cartButton.addEventListener(
    'click',
    async function() {

        if (!token) {

            window.location.href = '/login';

            return;
        }


        /*
        ==========================================================
        DÉSACTIVER LE BOUTON
        ==========================================================
        */

        cartButton.disabled = true;

        cartButton.textContent =
            'Ajout en cours...';


        try {

            const response =
                await fetch(
                    'http://127.0.0.1:8000/api/cart',
                    {
                        method: 'POST',

                        headers: {

                            'Accept':
                                'application/json',

                            'Content-Type':
                                'application/json',

                            'Authorization':
                                'Bearer ' + token

                        },

                        body: JSON.stringify({

                            product_id:
                                productId

                        })

                    }
                );


            const data =
                await response.json();


            if (!response.ok) {

                if (data.errors) {

                    const errors =
                        Object.values(
                            data.errors
                        )
                        .flat()
                        .join(' ');

                    throw new Error(errors);
                }


                throw new Error(
                    data.message ||
                    'Impossible d’ajouter le produit au panier.'
                );

            }


            /*
            ======================================================
            SUCCÈS
            ======================================================
            */

            console.log(
                'Produit ajouté au panier :',
                data
            );


            cartButton.textContent =
                '✓ Ajouté au panier';


            /*
            ------------------------------------------------------
            Petit délai avant de remettre le bouton
            ------------------------------------------------------
            */

            setTimeout(
                function() {

                    cartButton.textContent =
                        '🛒 Ajouter au panier';

                    cartButton.disabled =
                        false;

                },
                2000
            );


        } catch (error) {

            console.error(
                'Erreur panier :',
                error
            );


            alert(
                error.message ||
                'Une erreur est survenue.'
            );


            cartButton.textContent =
                '🛒 Ajouter au panier';

            cartButton.disabled =
                false;

        }

    }
);


    /*
    ==========================================================
    DÉMARRAGE
    ==========================================================
    */

    loadProduct();

</script>

@endsection
