@extends('layouts.app')

@section('title', 'Détails du produit - MarketCylia')

@section('content')

<div class="container">

    {{-- Retour --}}
    <div class="mb-4">

        <a
            href="/seller/products"
            class="btn btn-outline-secondary">

            ← Retour à mes produits

        </a>

    </div>


    {{-- Erreur --}}
    <div
        id="productError"
        class="alert alert-danger d-none">
    </div>


    {{-- Produit --}}
    <div id="productContainer">

        <div class="text-center py-5">

            <div class="spinner-border"></div>

            <p class="text-muted mt-3">
                Chargement du produit...
            </p>

        </div>

    </div>

</div>


<script>

    /*
    ==========================================================
    DÉTAILS DU PRODUIT
    ==========================================================
    */

    const token =
        localStorage.getItem('marketcylia_token');

    const productContainer =
        document.getElementById('productContainer');

    const productError =
        document.getElementById('productError');


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
    ID DU PRODUIT
    ==========================================================
    */

    const productId =
        @json($productId);


    /*
    ==========================================================
    CHARGEMENT DU PRODUIT
    ==========================================================
    */

    async function loadProduct() {

        try {

            const response =
                await fetch(
                    `http://127.0.0.1:8000/api/seller/products/${productId}`,
                    {
                        method: 'GET',

                        headers: {
                            'Accept': 'application/json',

                            'Authorization':
                                'Bearer ' + token
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


            const images =
                product.images || [];


            /*
            ==================================================
            IMAGE PRINCIPALE
            ==================================================
            */

            const primaryImage =
                images.find(
                    image => image.is_primary
                ) || images[0];


            const mainImage =
                primaryImage
                    ? `/storage/${primaryImage.image}`
                    : null;


            /*
            ==================================================
            GALERIE
            ==================================================
            */

            const gallery =
                images.map(
                    function(image, index) {

                        return `

                            <div class="col-6 col-md-4">

                                <div class="card border-0 shadow-sm">

                                    <img
                                        src="/storage/${image.image}"
                                        alt="${product.name}"
                                        class="card-img-top"
                                        style="
                                            height: 220px;
                                            object-fit: cover;
                                        "
                                    >

                                    <div
                                        class="card-body text-center">

                                        ${
                                            image.is_primary
                                            ?
                                            '<span class="badge text-bg-dark">Image principale</span>'
                                            :
                                            `<span class="badge text-bg-light">Image ${index + 1}</span>`
                                        }

                                    </div>

                                </div>

                            </div>

                        `;

                    }
                ).join('');
            /*
            ==================================================
            AFFICHAGE
            ==================================================
            */

            productContainer.innerHTML = `

                <div class="card border-0 shadow-sm">

                    <div class="card-body p-4 p-md-5">

                        <div class="row g-5">


                            {{-- Image principale --}}

                            <div class="col-lg-6">

                                ${
                                    mainImage
                                    ?
                                    `
                                        <img
                                            src="${mainImage}"
                                            alt="${product.name}"
                                            class="img-fluid rounded-4 shadow-sm w-100"
                                            style="
                                                max-height: 500px;
                                                object-fit: cover;
                                            "
                                        >
                                    `
                                    :
                                    `
                                        <div
                                            class="bg-light rounded-4 d-flex align-items-center justify-content-center"
                                            style="
                                                height: 400px;
                                            "
                                        >

                                            <span class="display-1">
                                                📷
                                            </span>

                                        </div>
                                    `
                                }

                            </div>


                            {{-- Informations --}}

                            <div class="col-lg-6">

                                <span class="badge text-bg-success mb-3">
                                    ${
                                        product.is_active
                                        ? 'Produit actif'
                                        : 'Produit inactif'
                                    }
                                </span>


                                <h1 class="fw-bold mb-3">
                                    ${product.name}
                                </h1>

                                <div class="mb-4">

    ${
        product.category
        ?
        `
            <span class="badge text-bg-light border">
                🏷️ ${product.category.name}
            </span>
        `
        :
        `
            <span class="text-muted small">
                Catégorie non définie
            </span>
        `
    }

</div>

                                <h2 class="fw-bold mb-4">
                                    ${product.price} DA
                                </h2>


                                <p class="text-muted mb-4">
                                    ${product.description ?? 'Aucune description.'}
                                </p>


                                <div class="mb-4">

                                    <strong>
                                        Stock :
                                    </strong>

                                    ${product.stock}

                                </div>


                                <div class="d-flex gap-2">

                                    <a
                                        href="/seller/products/${product.id}/edit"
                                        class="btn btn-dark">

                                        Modifier

                                    </a>

                                    <a
                                        href="/seller/products"
                                        class="btn btn-outline-secondary">

                                        Retour

                                    </a>

                                </div>

                            </div>

                        </div>


                        {{-- Galerie --}}

                        ${
                            images.length > 0
                            ?
                            `

                                <hr class="my-5">

                                <h3 class="fw-bold mb-4">
                                    Photos du produit
                                </h3>

                                <div class="row g-4">

                                    ${gallery}

                                </div>

                            `
                            :
                            ''
                        }

                    </div>

                </div>

            `;


        } catch (error) {

            console.error(
                'Erreur produit :',
                error
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
    DÉMARRAGE
    ==========================================================
    */

    loadProduct();

</script>

@endsection
