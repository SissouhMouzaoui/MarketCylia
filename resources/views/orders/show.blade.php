@extends('layouts.app')

@section('title', 'Détails de la commande - MarketCylia')

@section('content')

<div class="container">

    <div class="mb-4">

        <a
            href="/orders"
            class="btn btn-outline-secondary">

            ← Mes commandes

        </a>

    </div>


    <div
        id="orderError"
        class="alert alert-danger d-none">
    </div>


    <div
        id="loading"
        class="text-center py-5">

        <div class="spinner-border"></div>

        <p class="text-muted mt-3">
            Chargement de la commande...
        </p>

    </div>


    <div
        id="orderContainer"
        class="d-none">
    </div>

</div>


<script>

const token =
    localStorage.getItem('marketcylia_token');


const orderId =
    @json($orderId);


const loading =
    document.getElementById('loading');

const orderContainer =
    document.getElementById('orderContainer');

const orderError =
    document.getElementById('orderError');


if (!token) {

    window.location.href = '/login';

}


function getStatusLabel(status) {

    const labels = {

        pending: 'En attente',

        confirmed: 'Confirmée',

        shipped: 'Expédiée',

        delivered: 'Livrée',

        cancelled: 'Annulée'

    };

    return labels[status] || status;

}


function getStatusClass(status) {

    const classes = {

        pending: 'text-bg-warning',

        confirmed: 'text-bg-primary',

        shipped: 'text-bg-info',

        delivered: 'text-bg-success',

        cancelled: 'text-bg-danger'

    };

    return classes[status] || 'text-bg-secondary';

}


/*
==========================================================
CHARGER
==========================================================
*/

async function loadOrder() {

    try {

        const response =
            await fetch(
                `http://127.0.0.1:8000/api/orders/${orderId}`,
                {

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


        renderOrder(order);


        loading.classList.add(
            'd-none'
        );

        orderContainer.classList.remove(
            'd-none'
        );


    } catch (error) {

        loading.classList.add(
            'd-none'
        );

        orderError.textContent =
            error.message;

        orderError.classList.remove(
            'd-none'
        );

    }

}


/*
==========================================================
AFFICHAGE
==========================================================
*/

function renderOrder(order) {

    const items =
        order.items || [];


    orderContainer.innerHTML = `

        <div class="card border-0 shadow-sm">

            <div class="card-body p-4 p-md-5">

                <div
                    class="d-flex justify-content-between align-items-start mb-4">

                    <div>

                        <h1 class="fw-bold mb-2">

                            Commande

                        </h1>

                        <div class="text-muted">

                            ${order.order_number}

                        </div>

                    </div>

                    <span
                        class="badge ${getStatusClass(order.status)} fs-6">

                        ${getStatusLabel(order.status)}

                    </span>

                </div>


                <hr>


                <div class="d-flex flex-column gap-3 mt-4">

                    ${items.map(
                        item => `

                            <div
                                class="border rounded-3 p-3">

                                <div
                                    class="row align-items-center g-3">

                                    <div
                                        class="col-md-6">

                                        <h5
                                            class="fw-bold mb-1">

                                            ${item.product_name}

                                        </h5>

                                        <small
                                            class="text-muted">

                                            ${Number(
                                                item.price
                                            ).toFixed(2)}
                                            DA ×
                                            ${item.quantity}

                                        </small>

                                    </div>


                                    <div
                                        class="col-md-3">

                                        ${item.product?.user

                                            ? `

                                                <small
                                                    class="text-muted">

                                                    Vendeur

                                                </small>

                                                <div
                                                    class="fw-semibold">

                                                    ${item.product.user.name}

                                                </div>

                                            `

                                            : ''

                                        }

                                    </div>


                                    <div
                                        class="col-md-3 text-md-end">

                                        <strong>

                                            ${Number(
                                                item.subtotal
                                            ).toFixed(2)}
                                            DA

                                        </strong>

                                    </div>

                                </div>

                            </div>

                        `
                    ).join('')}

                </div>


                <hr class="my-4">


                <div
                    class="d-flex justify-content-between align-items-center">

                    <span
                        class="fs-5 fw-semibold">

                        Total

                    </span>

                    <strong
                        class="fs-3">

                        ${Number(
                            order.total
                        ).toFixed(2)}
                        DA

                    </strong>

                </div>


                ${
                    order.status === 'pending'

                    ?

                    `

                        <div class="mt-4">

                            <button
                                type="button"
                                class="btn btn-outline-danger"
                                onclick="cancelOrder()">

                                Annuler la commande

                            </button>

                        </div>

                    `

                    :

                    ''

                }

            </div>

        </div>

    `;

}


/*
==========================================================
ANNULER
==========================================================
*/

async function cancelOrder() {

    const confirmed =
        confirm(
            'Voulez-vous vraiment annuler cette commande ?'
        );


    if (!confirmed) {

        return;

    }


    try {

        const response =
            await fetch(
                `http://127.0.0.1:8000/api/orders/${orderId}/cancel`,
                {

                    method: 'PUT',

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
                'Impossible d’annuler la commande.'
            );

        }


        alert(
            data.message ||
            'Commande annulée avec succès.'
        );


        loadOrder();


    } catch (error) {

        alert(
            error.message
        );

    }

}


loadOrder();

</script>

@endsection
