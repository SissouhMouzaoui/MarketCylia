@extends('layouts.app')

@section('title', 'Mon panier - MarketCylia')

@section('content')

<div class="container">

    {{-- ==========================================================
         TITRE
    ========================================================== --}}

    <div class="mb-4">

        <h1 class="fw-bold">
            🛒 Mon panier
        </h1>

        <p class="text-muted">
            Gérez les produits que vous souhaitez acheter.
        </p>

    </div>


    {{-- ==========================================================
         ERREUR
    ========================================================== --}}

    <div
        id="cartError"
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
            Chargement du panier...
        </p>

    </div>


    {{-- ==========================================================
         PANIER
    ========================================================== --}}

    <div
        id="cartContainer"
        class="d-none">

        <div class="row g-4">


            {{-- ==================================================
                 PRODUITS
            ================================================== --}}

            <div class="col-lg-8">

                <div
                    id="cartItems"
                    class="d-flex flex-column gap-3">
                </div>

            </div>


            {{-- ==================================================
                 RÉSUMÉ
            ================================================== --}}

            <div class="col-lg-4">

                <div class="card border-0 shadow-sm">

                    <div class="card-body p-4">

                        <h4 class="fw-bold mb-4">
                            Résumé
                        </h4>


                        {{-- ARTICLES --}}

                        <div
                            class="d-flex justify-content-between mb-3">

                            <span>
                                Articles
                            </span>

                            <strong id="totalItems">
                                0
                            </strong>

                        </div>


                        <hr>


                        {{-- TOTAL --}}

                        <div
                            class="d-flex justify-content-between mb-4">

                            <span class="fw-semibold">
                                Total
                            </span>

                            <strong
                                id="cartTotal"
                                class="fs-4">

                                0 DA

                            </strong>

                        </div>


                        {{-- ==================================================
                             PASSER LA COMMANDE
                        ================================================== --}}

                        <button
                            type="button"
                            id="checkoutButton"
                            class="btn btn-dark w-100">

                            🛒 Passer la commande

                        </button>


                        {{-- CONTINUER ACHATS --}}

                        <a
                            href="/products"
                            class="btn btn-outline-secondary w-100 mt-2">

                            Continuer mes achats

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ==========================================================
         PANIER VIDE
    ========================================================== --}}

    <div
        id="emptyCart"
        class="d-none">

        <div class="card border-0 shadow-sm">

            <div class="card-body text-center py-5">

                <div class="display-1 mb-3">
                    🛒
                </div>

                <h3 class="fw-bold">
                    Votre panier est vide
                </h3>

                <p class="text-muted mb-4">
                    Vous n'avez encore ajouté aucun produit.
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
ELEMENTS DOM
==============================================================
*/

const loading =
    document.getElementById(
        'loading'
    );

const cartContainer =
    document.getElementById(
        'cartContainer'
    );

const cartItems =
    document.getElementById(
        'cartItems'
    );

const cartError =
    document.getElementById(
        'cartError'
    );

const emptyCart =
    document.getElementById(
        'emptyCart'
    );

const totalItems =
    document.getElementById(
        'totalItems'
    );

const cartTotal =
    document.getElementById(
        'cartTotal'
    );

const checkoutButton =
    document.getElementById(
        'checkoutButton'
    );


/*
==============================================================
VÉRIFICATION DU TOKEN
==============================================================
*/

if (!token) {

    window.location.href =
        '/login';

}


/*
==============================================================
AFFICHER UNE ERREUR
==============================================================
*/

function showError(message) {

    cartError.textContent =
        message ||
        'Une erreur est survenue.';

    cartError.classList.remove(
        'd-none'
    );

}


/*
==============================================================
MASQUER L'ERREUR
==============================================================
*/

function hideError() {

    cartError.classList.add(
        'd-none'
    );

    cartError.textContent =
        '';

}


/*
==============================================================
CHARGER LE PANIER
==============================================================
*/

async function loadCart() {

    try {

        hideError();


        const response =
            await fetch(
                'http://127.0.0.1:8000/api/cart',
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
                'Impossible de charger le panier.'
            );

        }


        const items =
            data.cart_items ||
            data.cart ||
            [];


        /*
        ======================================================
        PANIER VIDE
        ======================================================
        */

        if (items.length === 0) {

            loading.classList.add(
                'd-none'
            );

            cartContainer.classList.add(
                'd-none'
            );

            emptyCart.classList.remove(
                'd-none'
            );

            return;

        }


        /*
        ======================================================
        AFFICHER LE PANIER
        ======================================================
        */

        renderCart(items);


        loading.classList.add(
            'd-none'
        );

        emptyCart.classList.add(
            'd-none'
        );

        cartContainer.classList.remove(
            'd-none'
        );


    } catch (error) {

        console.error(
            'Erreur panier :',
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
AFFICHER LES PRODUITS
==============================================================
*/

function renderCart(items) {

    let total = 0;

    let quantityTotal = 0;


    cartItems.innerHTML =
        items.map(
            function(item) {

                const product =
                    item.product;


                /*
                --------------------------------------------------
                Produit introuvable
                --------------------------------------------------
                */

                if (!product) {

                    return '';

                }


                const quantity =
                    Number(
                        item.quantity
                    );


                const price =
                    Number(
                        product.price
                    );


                const subtotal =
                    price *
                    quantity;


                const stock =
                    Number(
                        product.stock
                    );


                total +=
                    subtotal;


                quantityTotal +=
                    quantity;


                /*
                --------------------------------------------------
                IMAGE
                --------------------------------------------------
                */

                const images =
                    product.images ||
                    [];


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


                /*
                --------------------------------------------------
                CATÉGORIE
                --------------------------------------------------
                */

                const categoryHtml =
                    product.category
                        ? `

                            <span
                                class="badge text-bg-light mb-2">

                                ${product.category.name}

                            </span>

                        `
                        : '';


                /*
                --------------------------------------------------
                HTML PRODUIT
                --------------------------------------------------
                */

                return `

                    <div
                        class="card border-0 shadow-sm">

                        <div
                            class="card-body p-3 p-md-4">

                            <div
                                class="row align-items-center g-3">


                                {{-- IMAGE --}}

                                <div
                                    class="col-4 col-md-3">

                                    ${
                                        imageUrl

                                        ?

                                        `

                                            <img
                                                src="${imageUrl}"
                                                alt="${product.name}"
                                                class="img-fluid rounded-3"
                                                style="
                                                    width: 100%;
                                                    height: 140px;
                                                    object-fit: cover;
                                                "
                                            >

                                        `

                                        :

                                        `

                                            <div
                                                class="bg-light rounded-3 d-flex align-items-center justify-content-center"
                                                style="
                                                    height: 140px;
                                                "
                                            >

                                                <span
                                                    class="display-5">

                                                    📷

                                                </span>

                                            </div>

                                        `
                                    }

                                </div>


                                {{-- INFORMATIONS --}}

                                <div
                                    class="col-8 col-md-9">


                                    <div
                                        class="d-flex justify-content-between align-items-start">


                                        <div>

                                            <h4
                                                class="fw-bold mb-2">

                                                ${product.name}

                                            </h4>


                                            ${categoryHtml}

                                        </div>


                                        {{-- SUPPRIMER --}}

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="removeCartItem(${item.id})">

                                            🗑️

                                        </button>

                                    </div>


                                    {{-- PRIX --}}

                                    <div
                                        class="mb-3">

                                        <strong>

                                            ${price.toFixed(2)}
                                            DA

                                        </strong>

                                    </div>


                                    {{-- QUANTITÉ --}}

                                    <div
                                        class="d-flex align-items-center gap-2 mb-3">


                                        <button
                                            type="button"
                                            class="btn btn-outline-secondary"
                                            onclick="changeQuantity(
                                                ${item.id},
                                                ${quantity - 1}
                                            )"
                                            ${quantity <= 1 ? 'disabled' : ''}>

                                            −

                                        </button>


                                        <span
                                            class="fw-bold px-3">

                                            ${quantity}

                                        </span>


                                        <button
                                            type="button"
                                            class="btn btn-outline-secondary"
                                            onclick="changeQuantity(
                                                ${item.id},
                                                ${quantity + 1}
                                            )"
                                            ${quantity >= stock ? 'disabled' : ''}>

                                            +

                                        </button>


                                        <small
                                            class="text-muted ms-2">

                                            Stock :
                                            ${stock}

                                        </small>

                                    </div>


                                    {{-- SOUS-TOTAL --}}

                                    <div>

                                        Sous-total :

                                        <strong>

                                            ${subtotal.toFixed(2)}
                                            DA

                                        </strong>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                `;

            }
        ).join('');


    /*
    ==========================================================
    RÉSUMÉ
    ==========================================================
    */

    totalItems.textContent =
        quantityTotal;


    cartTotal.textContent =
        `${total.toFixed(2)} DA`;

}


/*
==============================================================
MODIFIER LA QUANTITÉ
==============================================================
*/

async function changeQuantity(
    cartItemId,
    quantity
) {

    if (quantity < 1) {

        return;

    }


    try {

        hideError();


        const response =
            await fetch(
                `http://127.0.0.1:8000/api/cart/${cartItemId}`,
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

                            quantity:
                                quantity

                        })

                }
            );


        const data =
            await response.json();


        if (!response.ok) {

            throw new Error(
                data.message ||
                'Impossible de modifier la quantité.'
            );

        }


        /*
        ------------------------------------------------------
        Recharger le panier
        ------------------------------------------------------
        */

        await loadCart();


    } catch (error) {

        console.error(
            'Erreur quantité :',
            error
        );


        showError(
            error.message
        );

    }

}


/*
==============================================================
SUPPRIMER UN ARTICLE
==============================================================
*/

async function removeCartItem(
    cartItemId
) {

    const confirmed =
        confirm(
            'Êtes-vous sûr de vouloir supprimer ce produit du panier ?'
        );


    if (!confirmed) {

        return;

    }


    try {

        hideError();


        const response =
            await fetch(
                `http://127.0.0.1:8000/api/cart/${cartItemId}`,
                {
                    method: 'DELETE',

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
                'Impossible de supprimer le produit.'
            );

        }


        /*
        ------------------------------------------------------
        Recharger le panier
        ------------------------------------------------------
        */

        await loadCart();


    } catch (error) {

        console.error(
            'Erreur suppression :',
            error
        );


        showError(
            error.message
        );

    }

}


/*
==============================================================
PASSER LA COMMANDE
==============================================================
*/

async function createOrder() {

    /*
    ----------------------------------------------------------
    Confirmation
    ----------------------------------------------------------
    */

    const confirmed =
        confirm(
            'Voulez-vous confirmer votre commande ?'
        );


    if (!confirmed) {

        return;

    }


    /*
    ----------------------------------------------------------
    Désactiver le bouton
    ----------------------------------------------------------
    */

    checkoutButton.disabled =
        true;

    checkoutButton.textContent =
        'Création de la commande...';


    try {

        hideError();


        /*
        ------------------------------------------------------
        Token
        ------------------------------------------------------
        */

        const currentToken =
            localStorage.getItem(
                'marketcylia_token'
            );


        if (!currentToken) {

            window.location.href =
                '/login';

            return;

        }


        /*
        ------------------------------------------------------
        CRÉER LES COMMANDES
        ------------------------------------------------------
        */

        const response =
            await fetch(
                'http://127.0.0.1:8000/api/orders',
                {
                    method: 'POST',

                    headers: {

                        'Accept':
                            'application/json',

                        'Authorization':
                            'Bearer ' + currentToken

                    }

                }
            );


        /*
        ------------------------------------------------------
        Lire la réponse
        ------------------------------------------------------
        */

        const data =
            await response.json();


        /*
        ------------------------------------------------------
        Vérifier erreur
        ------------------------------------------------------
        */

        if (!response.ok) {

            throw new Error(
                data.message ||
                'Impossible de créer la commande.'
            );

        }


        /*
        ======================================================
        IMPORTANT :
        Plusieurs vendeurs = plusieurs commandes
        ======================================================
        */

        if (
            data.orders &&
            data.orders.length > 1
        ) {

            /*
            --------------------------------------------------
            Plusieurs commandes
            --------------------------------------------------
            */

            window.location.href =
                '/orders';

            return;

        }


        /*
        ======================================================
        UNE SEULE COMMANDE
        ======================================================
        */

        if (
            data.orders &&
            data.orders.length === 1
        ) {

            window.location.href =
                `/orders/${data.orders[0].id}`;

            return;

        }


        /*
        ======================================================
        COMPATIBILITÉ ANCIENNE RÉPONSE API
        ======================================================
        */

        if (data.order) {

            window.location.href =
                `/orders/${data.order.id}`;

            return;

        }


        /*
        ======================================================
        AUCUNE COMMANDE
        ======================================================
        */

        window.location.href =
            '/orders';


    } catch (error) {

        console.error(
            'Erreur création commande :',
            error
        );


        showError(
            error.message ||
            'Impossible de créer la commande.'
        );


        /*
        ------------------------------------------------------
        Réactiver le bouton
        ------------------------------------------------------
        */

        checkoutButton.disabled =
            false;

        checkoutButton.textContent =
            '🛒 Passer la commande';

    }

}


/*
==============================================================
ÉVÉNEMENT DU BOUTON
==============================================================
*/

checkoutButton.addEventListener(
    'click',
    createOrder
);


/*
==============================================================
DÉMARRAGE
==============================================================
*/

loadCart();

</script>

@endsection
