@extends('layouts.app')

@section('title', 'Commandes - MarketCylia')

@section('content')

<div class="container">

    <div class="mb-4">

        <h1 class="fw-bold">
            📦 Commandes
        </h1>

        <p class="text-muted">
            Commandes contenant vos produits.
        </p>

    </div>


    <div
        id="error"
        class="alert alert-danger d-none">
    </div>


    <div
        id="loading"
        class="text-center py-5">

        <div class="spinner-border"></div>

        <p class="text-muted mt-3">
            Chargement...
        </p>

    </div>


    <div
        id="orders"
        class="d-none">
    </div>

</div>


<script>

const token =
    localStorage.getItem('marketcylia_token');


const loading =
    document.getElementById('loading');

const ordersContainer =
    document.getElementById('orders');

const errorBox =
    document.getElementById('error');


if (!token) {

    window.location.href = '/login';

}


function statusLabel(status) {

    return {

        pending: 'En attente',

        confirmed: 'Confirmée',

        shipped: 'Expédiée',

        delivered: 'Livrée',

        cancelled: 'Annulée'

    }[status] || status;

}


function statusClass(status) {

    return {

        pending: 'text-bg-warning',

        confirmed: 'text-bg-primary',

        shipped: 'text-bg-info',

        delivered: 'text-bg-success',

        cancelled: 'text-bg-danger'

    }[status] || 'text-bg-secondary';

}


async function loadSellerOrders() {

    try {

        const response =
            await fetch(
                'http://127.0.0.1:8000/api/seller/orders',
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
                'Impossible de charger les commandes.'
            );

        }


        const orders =
            data.orders || [];


        loading.classList.add(
            'd-none'
        );


        if (orders.length === 0) {

            ordersContainer.innerHTML = `

                <div
                    class="card border-0 shadow-sm">

                    <div
                        class="card-body text-center py-5">

                        <div class="display-1">
                            📦
                        </div>

                        <h3 class="fw-bold mt-3">
                            Aucune commande
                        </h3>

                        <p class="text-muted">
                            Vous n'avez encore reçu aucune commande.
                        </p>

                    </div>

                </div>

            `;

            ordersContainer.classList.remove(
                'd-none'
            );

            return;

        }


        ordersContainer.innerHTML =
            orders.map(
                order => `

                    <div
                        class="card border-0 shadow-sm mb-3">

                        <div
                            class="card-body p-4">

                            <div
                                class="row align-items-center g-3">

                                <div class="col-md-3">

                                    <small class="text-muted">
                                        Commande
                                    </small>

                                    <div class="fw-bold">
                                        ${order.order_number}
                                    </div>

                                </div>


                                <div class="col-md-3">

                                    <small class="text-muted">
                                        Client
                                    </small>

                                    <div>
                                        ${order.user?.name || 'Client'}
                                    </div>

                                </div>


                                <div class="col-md-2">

                                    <small class="text-muted">
                                        Total
                                    </small>

                                    <div class="fw-bold">
                                        ${Number(order.total).toFixed(2)}
                                        DA
                                    </div>

                                </div>


                                <div class="col-md-2">

                                    <span
                                        class="badge ${statusClass(order.status)}">

                                        ${statusLabel(order.status)}

                                    </span>

                                </div>


                                <div
                                    class="col-md-2 text-md-end">

                                    <a
                                        href="/seller/orders/${order.id}"
                                        class="btn btn-dark btn-sm">

                                        Gérer

                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>

                `
            )
            .join('');


        ordersContainer.classList.remove(
            'd-none'
        );


    } catch (error) {

        loading.classList.add(
            'd-none'
        );

        errorBox.textContent =
            error.message;

        errorBox.classList.remove(
            'd-none'
        );

    }

}


loadSellerOrders();

</script>

@endsection
