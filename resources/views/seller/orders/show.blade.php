@extends('layouts.app')

@section('title', 'Détails de la commande - MarketCylia')

@section('content')

<div class="container">

    {{-- ==========================================================
         RETOUR
    ========================================================== --}}

    <div class="mb-4">

        <a
            href="/seller/orders"
            class="btn btn-outline-secondary"
        >
            ← Retour aux commandes
        </a>

    </div>


    {{-- ==========================================================
         ERREUR
    ========================================================== --}}

    <div
        id="orderError"
        class="alert alert-danger d-none"
    ></div>


    {{-- ==========================================================
         SUCCÈS
    ========================================================== --}}

    <div
        id="orderSuccess"
        class="alert alert-success d-none"
    ></div>


    {{-- ==========================================================
         CHARGEMENT
    ========================================================== --}}

    <div
        id="loading"
        class="text-center py-5"
    >

        <div class="spinner-border"></div>

        <p class="text-muted mt-3">
            Chargement de la commande...
        </p>

    </div>


    {{-- ==========================================================
         COMMANDE
    ========================================================== --}}

    <div
        id="orderContainer"
        class="d-none"
    ></div>

</div>


<script>

/*
==============================================================
TOKEN
==============================================================
*/

const token =
    localStorage.getItem(
        'marketcylia_token'
    );


/*
==============================================================
ID COMMANDE
==============================================================
*/

const orderId =
    @json($orderId);


/*
==============================================================
ELEMENTS
==============================================================
*/

const loading =
    document.getElementById(
        'loading'
    );

const orderContainer =
    document.getElementById(
        'orderContainer'
    );

const orderError =
    document.getElementById(
        'orderError'
    );

const orderSuccess =
    document.getElementById(
        'orderSuccess'
    );


/*
==============================================================
VÉRIFIER TOKEN
==============================================================
*/

if (!token) {

    window.location.href =
        '/login';

}


/*
==============================================================
AFFICHER ERREUR
==============================================================
*/

function showError(message) {

    orderError.textContent =
        message ||
        'Une erreur est survenue.';

    orderError.classList.remove(
        'd-none'
    );

}


/*
==============================================================
CACHER ERREUR
==============================================================
*/

function hideError() {

    orderError.classList.add(
        'd-none'
    );

    orderError.textContent = '';

}


/*
==============================================================
AFFICHER SUCCÈS
==============================================================
*/

function showSuccess(message) {

    orderSuccess.textContent =
        message;

    orderSuccess.classList.remove(
        'd-none'
    );

}


/*
==============================================================
CACHER SUCCÈS
==============================================================
*/

function hideSuccess() {

    orderSuccess.classList.add(
        'd-none'
    );

    orderSuccess.textContent = '';

}


/*
==============================================================
LABEL STATUT
==============================================================
*/

function getStatusLabel(status) {

    if (status === 'pending') {

        return 'En attente';

    }

    if (status === 'confirmed') {

        return 'Confirmée';

    }

    if (status === 'shipped') {

        return 'Expédiée';

    }

    if (status === 'delivered') {

        return 'Livrée';

    }

    if (status === 'cancelled') {

        return 'Annulée';

    }

    return status;

}


/*
==============================================================
CLASSE BADGE
==============================================================
*/

function getBadgeClass(status) {

    if (status === 'pending') {

        return 'text-bg-warning';

    }

    if (status === 'confirmed') {

        return 'text-bg-primary';

    }

    if (status === 'shipped') {

        return 'text-bg-info';

    }

    if (status === 'delivered') {

        return 'text-bg-success';

    }

    if (status === 'cancelled') {

        return 'text-bg-danger';

    }

    return 'text-bg-secondary';

}


/*
==============================================================
CHARGER LA COMMANDE
==============================================================
*/

async function loadOrder() {

    try {

        hideError();

        hideSuccess();


        const response =
            await fetch(
                `http://127.0.0.1:8000/api/seller/orders/${orderId}`,
                {

                    method: 'GET',

                    headers: {

                        'Accept':
                            'application/json',

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
                'Impossible de charger la commande.'
            );

        }


        const order =
            data.order;


        /*
        ======================================================
        CLIENT
        ======================================================
        */

        const customer =
            order.user
                ? (
                    order.user.name ||
                    order.user.email ||
                    'Client'
                )
                : 'Client';


        /*
        ======================================================
        PRODUITS
        ======================================================
        */

        const items =
            order.items || [];


        const itemsHtml =
            items.map(
                function(item) {

                    const product =
                        item.product;


                    const productName =
                        item.product_name ||
                        product?.name ||
                        'Produit';


                    const price =
                        Number(
                            item.price
                        );


                    const quantity =
                        Number(
                            item.quantity
                        );


                    const subtotal =
                        Number(
                            item.subtotal
                        );


                    /*
                    --------------------------------------------------
                    IMAGE
                    --------------------------------------------------
                    */

                    const images =
                        product?.images || [];


                    const primaryImage =
                        images.find(
                            function(image) {

                                return image.is_primary;

                            }
                        ) ||
                        images[0];


                    const imageUrl =
                        primaryImage
                            ? `/storage/${primaryImage.image}`
                            : null;


                    return `

                        <div
                            class="card border-0 shadow-sm mb-3"
                        >

                            <div
                                class="card-body"
                            >

                                <div
                                    class="row align-items-center g-3"
                                >


                                    <div
                                        class="col-4 col-md-2"
                                    >

                                        ${
                                            imageUrl

                                            ?

                                            `

                                                <img
                                                    src="${imageUrl}"
                                                    alt="${productName}"
                                                    class="img-fluid rounded-3"
                                                    style="
                                                        width: 100%;
                                                        height: 100px;
                                                        object-fit: cover;
                                                    "
                                                >

                                            `

                                            :

                                            `

                                                <div
                                                    class="bg-light rounded-3 d-flex align-items-center justify-content-center"
                                                    style="
                                                        height: 100px;
                                                    "
                                                >

                                                    📷

                                                </div>

                                            `
                                        }

                                    </div>


                                    <div
                                        class="col-8 col-md-6"
                                    >

                                        <h5
                                            class="fw-bold mb-2"
                                        >

                                            ${productName}

                                        </h5>


                                        <div
                                            class="text-muted"
                                        >

                                            ${price.toFixed(2)} DA
                                            ×
                                            ${quantity}

                                        </div>

                                    </div>


                                    <div
                                        class="col-md-4 text-md-end"
                                    >

                                        <strong
                                            class="fs-5"
                                        >

                                            ${subtotal.toFixed(2)} DA

                                        </strong>

                                    </div>

                                </div>

                            </div>

                        </div>

                    `;

                }
            ).join('');


        /*
        ======================================================
        AFFICHER LA COMMANDE
        ======================================================
        */

        orderContainer.innerHTML = `

            <div class="card border-0 shadow-sm">

                <div class="card-body p-4 p-md-5">


                    {{-- ==================================================
                         EN-TÊTE
                    ================================================== --}}

                    <div
                        class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4"
                    >

                        <div>

                            <h1
                                class="fw-bold mb-2"
                            >

                                Commande

                            </h1>


                            <div
                                class="text-muted"
                            >

                                Numéro :

                                <strong>
                                    ${order.order_number}
                                </strong>

                            </div>

                        </div>


                        <span
                            class="badge ${getBadgeClass(order.status)} fs-6"
                        >

                            ${getStatusLabel(order.status)}

                        </span>

                    </div>


                    <hr>


                    {{-- ==================================================
                         CLIENT
                    ================================================== --}}

                    <div class="mb-4">

                        <h4
                            class="fw-bold mb-3"
                        >

                            👤 Client

                        </h4>


                        <div
                            class="bg-light rounded-3 p-3"
                        >

                            ${customer}

                        </div>

                    </div>


                    {{-- ==================================================
                         MODIFIER LE STATUT
                    ================================================== --}}

                    <div class="card bg-light border-0 mb-4">

                        <div class="card-body p-4">

                            <h4
                                class="fw-bold mb-3"
                            >

                                🔄 Gestion de la commande

                            </h4>


                            <div class="row align-items-end g-3">

                                <div class="col-md-8">

                                    <label
                                        for="statusSelect"
                                        class="form-label fw-semibold"
                                    >

                                        Statut de la commande

                                    </label>


                                    <select
                                        id="statusSelect"
                                        class="form-select"
                                    >

                                        <option
                                            value="pending"
                                            ${order.status === 'pending' ? 'selected' : ''}
                                        >
                                            En attente
                                        </option>


                                        <option
                                            value="confirmed"
                                            ${order.status === 'confirmed' ? 'selected' : ''}
                                        >
                                            Confirmée
                                        </option>


                                        <option
                                            value="shipped"
                                            ${order.status === 'shipped' ? 'selected' : ''}
                                        >
                                            Expédiée
                                        </option>


                                        <option
                                            value="delivered"
                                            ${order.status === 'delivered' ? 'selected' : ''}
                                        >
                                            Livrée
                                        </option>


                                        <option
                                            value="cancelled"
                                            ${order.status === 'cancelled' ? 'selected' : ''}
                                        >
                                            Annulée
                                        </option>

                                    </select>

                                </div>


                                <div class="col-md-4">

                                    <button
                                        type="button"
                                        id="updateStatusButton"
                                        class="btn btn-dark w-100"
                                    >

                                        Mettre à jour

                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- ==================================================
                         PRODUITS
                    ================================================== --}}

                    <div class="mb-4">

                        <h4
                            class="fw-bold mb-3"
                        >

                            📦 Produits commandés

                        </h4>


                        ${itemsHtml}

                    </div>


                    {{-- ==================================================
                         TOTAL
                    ================================================== --}}

                    <div
                        class="border-top pt-4"
                    >

                        <div
                            class="d-flex justify-content-between align-items-center"
                        >

                            <span
                                class="fw-semibold fs-5"
                            >

                                Total de la commande

                            </span>


                            <strong
                                class="fs-3"
                            >

                                ${Number(order.total).toFixed(2)}
                                DA

                            </strong>

                        </div>

                    </div>


                </div>

            </div>

        `;


        /*
        ======================================================
        CACHER CHARGEMENT
        ======================================================
        */

        loading.classList.add(
            'd-none'
        );


        orderContainer.classList.remove(
            'd-none'
        );


        /*
        ======================================================
        BOUTON STATUT
        ======================================================
        */

        document
            .getElementById(
                'updateStatusButton'
            )
            .addEventListener(
                'click',
                updateOrderStatus
            );


    }

    catch (error) {

        console.error(
            'Erreur commande vendeur :',
            error
        );


        loading.classList.add(
            'd-none'
        );


        showError(
            error.message
        );

    }

}


/*
==============================================================
MODIFIER LE STATUT
==============================================================
*/

async function updateOrderStatus() {

    try {

        hideError();

        hideSuccess();


        const statusSelect =
            document.getElementById(
                'statusSelect'
            );


        const updateButton =
            document.getElementById(
                'updateStatusButton'
            );


        const status =
            statusSelect.value;


        /*
        ------------------------------------------------------
        Confirmation
        ------------------------------------------------------
        */

        const confirmed =
            confirm(
                `Voulez-vous modifier le statut de cette commande vers "${getStatusLabel(status)}" ?`
            );


        if (!confirmed) {

            return;

        }


        /*
        ------------------------------------------------------
        Désactiver le bouton
        ------------------------------------------------------
        */

        updateButton.disabled =
            true;

        updateButton.textContent =
            'Mise à jour...';


        /*
        ------------------------------------------------------
        API
        ------------------------------------------------------
        */

        const response =
            await fetch(
                `http://127.0.0.1:8000/api/seller/orders/${orderId}/status`,
                {

                    method: 'PUT',

                    headers: {

                        'Accept':
                            'application/json',

                        'Content-Type':
                            'application/json',

                        'Authorization':
                            'Bearer ' + token

                    },

                    body:
                        JSON.stringify({

                            status:
                                status

                        })

                }
            );


        const data =
            await response.json();


        /*
        ------------------------------------------------------
        Vérifier réponse
        ------------------------------------------------------
        */

        if (!response.ok) {

            throw new Error(
                data.message ||
                'Impossible de modifier le statut.'
            );

        }


        /*
        ------------------------------------------------------
        Succès
        ------------------------------------------------------
        */

        showSuccess(
            data.message ||
            'Statut de la commande mis à jour.'
        );


        /*
        ------------------------------------------------------
        Recharger les données
        ------------------------------------------------------
        */

        await loadOrder();


    }

    catch (error) {

        console.error(
            'Erreur statut commande :',
            error
        );


        showError(
            error.message
        );

    }

}


/*
==============================================================
DÉMARRAGE
==============================================================
*/

loadOrder();

</script>

@endsection
