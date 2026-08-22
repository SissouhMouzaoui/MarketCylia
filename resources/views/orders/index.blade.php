@extends('layouts.app')

@section('title', 'Mes commandes - MarketCylia')

@section('content')

<div class="container">

    <div class="mb-4">

        <h1 class="fw-bold">
            📦 Mes commandes
        </h1>

        <p class="text-muted">
            Retrouvez ici toutes vos commandes.
        </p>

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
            Chargement de vos commandes...
        </p>

    </div>


    <div
        id="ordersContainer"
        class="d-none">
    </div>


    <div
        id="emptyOrders"
        class="d-none">

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center py-5">

                <div class="display-1 mb-3">
                    📦
                </div>

                <h3 class="fw-bold">
                    Aucune commande
                </h3>

                <p class="text-muted mb-4">
                    Vous n'avez encore passé aucune commande.
                </p>

                <a
                    href="/products"
                    class="btn btn-dark">

                    Découvrir les produits

                </a>

            </div>

        </div>

    </div>

</div>


<script>

const token =
    localStorage.getItem('marketcylia_token');


const loading =
    document.getElementById('loading');

const ordersContainer =
    document.getElementById('ordersContainer');

const emptyOrders =
    document.getElementById('emptyOrders');

const orderError =
    document.getElementById('orderError');


if (!token) {

    window.location.href = '/login';

}


/*
==========================================================
STATUT
==========================================================
*/

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
CHARGER LES COMMANDES
==========================================================
*/

async function loadOrders() {

    try {

        const response =
            await fetch(
                'http://127.0.0.1:8000/api/orders',
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
                'Impossible de charger les commandes.'
            );

        }


        const orders =
            data.orders || [];


        loading.classList.add('d-none');


        if (orders.length === 0) {

            emptyOrders.classList.remove(
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

                                <div
                                    class="col-md-3">

                                    <small
                                        class="text-muted">

                                        Numéro de commande

                                    </small>

                                    <div
                                        class="fw-bold">

                                        ${order.order_number}

                                    </div>

                                </div>


                                <div
                                    class="col-md-2">

                                    <small
                                        class="text-muted">

                                        Date

                                    </small>

                                    <div>

                                        ${new Date(
                                            order.created_at
                                        ).toLocaleDateString('fr-FR')}

                                    </div>

                                </div>


                                <div
                                    class="col-md-2">

                                    <small
                                        class="text-muted">

                                        Articles

                                    </small>

                                    <div>

                                        ${order.items
                                            ? order.items.length
                                            : 0}

                                    </div>

                                </div>


                                <div
                                    class="col-md-2">

                                    <small
                                        class="text-muted">

                                        Total

                                    </small>

                                    <div
                                        class="fw-bold">

                                        ${Number(
                                            order.total
                                        ).toFixed(2)}
                                        DA

                                    </div>

                                </div>


                                <div
                                    class="col-md-3 text-md-end">

                                    <span
                                        class="badge ${getStatusClass(order.status)} mb-2">

                                        ${getStatusLabel(order.status)}

                                    </span>

                                    <br>

                                    <a
                                        href="/orders/${order.id}"
                                        class="btn btn-dark btn-sm">

                                        Voir les détails

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

        console.error(
            'Erreur commandes :',
            error
        );


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


loadOrders();

</script>

@endsection
