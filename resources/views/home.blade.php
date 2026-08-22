@extends('layouts.app')

@section('title', 'Accueil - MarketCylia')

@section('content')

{{-- ==========================================================
     MESSAGE DE BIENVENUE
========================================================== --}}

<div
    id="homeWelcomeUser"
    class="alert alert-success d-none"
>

    <strong>
        Bienvenue,
        <span id="homeWelcomeUserName"></span> !
    </strong>

    <span class="ms-1">
        Nous sommes heureux de vous retrouver sur MarketCylia.
    </span>

</div>


{{-- ==========================================================
     HERO
========================================================== --}}

<section class="py-5">

    <div class="row align-items-center">

        {{-- Texte --}}
        <div class="col-md-7">

            <h1 class="display-4 fw-bold">
                Bienvenue sur MarketCylia
            </h1>

            <p class="lead text-muted mt-3">
                Découvrez des produits proposés par différents vendeurs
                et profitez d'une expérience d'achat simple et agréable.
            </p>

            <div class="mt-4">

                <a
                    href="/products"
                    class="btn btn-dark btn-lg me-2"
                >
                    Découvrir les produits
                </a>

                <a
                    href="/register?role=seller"
                    class="btn btn-outline-dark btn-lg"
                >
                    Devenir vendeur
                </a>

            </div>

        </div>


        {{-- Illustration --}}
        <div class="col-md-5 text-center mt-4 mt-md-0">

            <div class="bg-white rounded-4 shadow-sm p-5">

                <div class="display-1">
                    🛍️
                </div>

                <h3 class="mt-3">
                    MarketCylia
                </h3>

                <p class="text-muted mb-0">
                    Votre marketplace multi-vendeurs
                </p>

            </div>

        </div>

    </div>

</section>


{{-- ==========================================================
     FEATURES
========================================================== --}}

<section class="py-5">

    <div class="text-center mb-4">

        <h2 class="fw-bold">
            Pourquoi MarketCylia ?
        </h2>

        <p class="text-muted">
            Une plateforme pensée pour les acheteurs et les vendeurs.
        </p>

    </div>


    <div class="row g-4">


        {{-- ==================================================
             ACHETEUR
        ================================================== --}}

        <div class="col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body text-center p-4">

                    <div class="fs-1 mb-3">
                        🛒
                    </div>

                    <h5 class="fw-bold">
                        Achetez facilement
                    </h5>

                    <p class="text-muted">
                        Parcourez les produits et trouvez facilement
                        ce que vous recherchez.
                    </p>

                    <a
                        href="/products"
                        class="btn btn-outline-dark"
                    >
                        Voir les produits
                    </a>

                </div>

            </div>

        </div>


        {{-- ==================================================
             VENDEUR
        ================================================== --}}

        <div class="col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body text-center p-4">

                    <div class="fs-1 mb-3">
                        🏪
                    </div>

                    <h5 class="fw-bold">
                        Vendez vos produits
                    </h5>

                    <p class="text-muted">
                        Créez votre compte vendeur et gérez vos produits
                        depuis votre espace vendeur.
                    </p>

                    <a
                        href="/register?role=seller"
                        class="btn btn-outline-dark"
                    >
                        Devenir vendeur
                    </a>

                </div>

            </div>

        </div>


        {{-- ==================================================
             SÉCURITÉ
        ================================================== --}}

        <div class="col-md-4">

            <div class="card border-0 shadow-sm h-100">

                <div class="card-body text-center p-4">

                    <div class="fs-1 mb-3">
                        🔒
                    </div>

                    <h5 class="fw-bold">
                        Une plateforme sécurisée
                    </h5>

                    <p class="text-muted">
                        Les comptes et les fonctionnalités sont protégés
                        selon le rôle de chaque utilisateur.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>
<script>

/*
==========================================================
UTILISATEUR CONNECTÉ SUR LA PAGE D'ACCUEIL
==========================================================
*/


/*
==========================================================
RÉCUPÉRER LES ÉLÉMENTS
==========================================================
*/

const homeWelcomeBox =
    document.getElementById('homeWelcomeUser');

const homeWelcomeName =
    document.getElementById('homeWelcomeUserName');


/*
==========================================================
VÉRIFIER QUE LES ÉLÉMENTS EXISTENT
==========================================================
*/

if (
    homeWelcomeBox &&
    homeWelcomeName
) {

    /*
    ------------------------------------------------------
    Récupérer l'utilisateur enregistré après connexion
    ------------------------------------------------------
    */

    const storedUser =
        localStorage.getItem(
            'marketcylia_user'
        );


    /*
    ------------------------------------------------------
    Aucun utilisateur connecté
    ------------------------------------------------------
    */

    if (!storedUser) {

        homeWelcomeBox.classList.add(
            'd-none'
        );

    } else {

        try {

            /*
            --------------------------------------------------
            Convertir les données JSON
            --------------------------------------------------
            */

            const user =
                JSON.parse(storedUser);


            /*
            --------------------------------------------------
            Vérifier les informations
            --------------------------------------------------
            */

            if (
                user &&
                user.name
            ) {

                /*
                ------------------------------------------------
                Afficher le nom de l'utilisateur
                ------------------------------------------------
                */

                homeWelcomeName.textContent =
                    user.name;


                homeWelcomeBox.classList.remove(
                    'd-none'
                );


                /*
                ------------------------------------------------
                Debug
                ------------------------------------------------
                */

                console.log(
                    'Utilisateur affiché sur accueil :',
                    user
                );

            } else {

                homeWelcomeBox.classList.add(
                    'd-none'
                );

            }


        } catch (error) {

            console.error(
                'Erreur lecture utilisateur local :',
                error
            );


            homeWelcomeBox.classList.add(
                'd-none'
            );

        }

    }

}

</script>


@endsection
