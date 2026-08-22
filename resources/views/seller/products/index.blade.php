@extends('layouts.app')

@section('title', 'Mes produits - MarketCylia')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <div>
        <h1 class="fw-bold mb-1">
            Mes produits
        </h1>

        <p class="text-muted mb-0">
            Gérez les produits de votre boutique.
        </p>
    </div>

    <a
        href="/seller/products/create"
        class="btn btn-dark">

        + Ajouter un produit

    </a>

</div>


{{-- Message d'erreur --}}
<div
    id="productsError"
    class="alert alert-danger d-none">
</div>


{{-- Liste des produits --}}
<div id="productsContainer">

    <div class="text-center py-5">

        <div class="spinner-border"></div>

        <p class="text-muted mt-3">
            Chargement des produits...
        </p>

    </div>

</div>


<script>

    /*
    ==========================================================
    MES PRODUITS
    ==========================================================
    */

    const token =
        localStorage.getItem('marketcylia_token');

    const productsContainer =
        document.getElementById('productsContainer');

    const productsError =
        document.getElementById('productsError');


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
                    'Impossible de charger les produits.'
                );

            }


            const products =
                data.products || [];


            /*
            ==================================================
            Aucun produit
            ==================================================
            */

            if (products.length === 0) {

                productsContainer.innerHTML = `

                    <div class="card border-0 shadow-sm">

                        <div class="card-body text-center py-5">

                            <div class="display-4">
                                📦
                            </div>

                            <h4 class="mt-3">
                                Aucun produit
                            </h4>

                            <p class="text-muted">
                                Vous n'avez pas encore ajouté de produit.
                            </p>

                            <a
                                href="/seller/products/create"
                                class="btn btn-dark">

                                Ajouter mon premier produit

                            </a>

                        </div>

                    </div>

                `;

                return;

            }


            /*
            ==================================================
            AFFICHAGE DES PRODUITS
            ==================================================
            */

            productsContainer.innerHTML = `

                <div class="card border-0 shadow-sm">

                    <div class="table-responsive">

                        <table class="table table-hover align-middle mb-0">

                            <thead class="table-light">

                                <tr>

                                    <th>
                                        Produit
                                    </th>

                                    <th>
                                        Prix
                                    </th>

                                    <th>
                                        Stock
                                    </th>

                                    <th>
                                        Statut
                                    </th>

                                    <th class="text-end">
                                        Actions
                                    </th>

                                </tr>

                            </thead>

                            <tbody id="productsTableBody">

                            </tbody>

                        </table>

                    </div>

                </div>

            `;


            const tableBody =
                document.getElementById('productsTableBody');


            products.forEach(function(product) {

                const statusBadge =
                    product.is_active
                        ? '<span class="badge text-bg-success">Actif</span>'
                        : '<span class="badge text-bg-secondary">Inactif</span>';


                tableBody.innerHTML += `

                    <tr>

                        <td>

    <a
        href="/seller/products/${product.id}"
        class="text-decoration-none text-dark">

        <div class="d-flex align-items-center gap-3">

            ${
                product.images && product.images.length > 0
                ?
                `
                    <img
                        src="/storage/${product.images.find(image => image.is_primary)?.image ?? product.images[0].image}"
                        alt="${product.name}"
                        style="
                            width: 70px;
                            height: 70px;
                            object-fit: cover;
                            border-radius: 10px;
                        "
                    >
                `
                :
                `
                    <div
                        class="d-flex align-items-center justify-content-center bg-light"
                        style="
                            width: 70px;
                            height: 70px;
                            border-radius: 10px;
                        "
                    >
                        📷
                    </div>
                `
            }

            <div>

                <div class="fw-semibold">
                    ${product.name}
                </div>

                <small class="text-muted">
                    ${product.description ?? ''}
                </small>

            </div>

        </div>

    </a>

</td>


                        <td>

                            <strong>
                                ${product.price} DA
                            </strong>

                        </td>


                        <td>

                            ${product.stock}

                        </td>


                        <td>

                            ${statusBadge}

                        </td>


                        <td class="text-end">

                            <a
                                href="/seller/products/${product.id}/edit"
                                class="btn btn-sm btn-outline-dark me-1">

                                Modifier

                            </a>

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-danger"
                                onclick="deleteProduct(${product.id})">

                                Supprimer

                            </button>

                        </td>

                    </tr>

                `;

            });


        } catch (error) {

            console.error(
                'Erreur produits :',
                error
            );


            productsError.textContent =
                error.message ||
                'Une erreur est survenue.';

            productsError.classList.remove(
                'd-none'
            );

        }

    }

/*
==========================================================
SUPPRESSION D'UN PRODUIT
==========================================================
*/

async function deleteProduct(productId) {

    /*
    --------------------------------------------------
    Confirmation avant suppression
    --------------------------------------------------
    */

    const confirmed = confirm(
        'Êtes-vous sûr de vouloir supprimer ce produit ?'
    );


    if (!confirmed) {

        return;

    }


    try {

        const response = await fetch(
            `http://127.0.0.1:8000/api/seller/products/${productId}`,
            {
                method: 'DELETE',

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
                'Impossible de supprimer le produit.'
            );

        }


        console.log(
            'Produit supprimé :',
            data
        );


        /*
        --------------------------------------------------
        Recharger la liste
        --------------------------------------------------
        */

        loadProducts();


    } catch (error) {

        console.error(
            'Erreur suppression :',
            error
        );


        productsError.textContent =
            error.message ||
            'Une erreur est survenue.';

        productsError.classList.remove(
            'd-none'
        );

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
