@extends('layouts.app')

@section('title', 'Connexion - MarketCylia')

@section('content')

<div class="row justify-content-center">

```
<div class="col-md-6 col-lg-5">

    <div class="card border-0 shadow-sm">

        <div class="card-body p-5">

            {{-- ==================================================
                 TITRE
            ================================================== --}}

            <div class="text-center mb-4">

                <div class="display-4 mb-3">
                    🛍️
                </div>

                <h2 class="fw-bold">
                    Connexion
                </h2>

                <p class="text-muted">
                    Connectez-vous à votre compte MarketCylia
                </p>

            </div>


            {{-- ==================================================
                 MESSAGE D'ERREUR
            ================================================== --}}

            <div
                id="loginError"
                class="alert alert-danger d-none"
                role="alert">
            </div>


            {{-- ==================================================
                 MESSAGE DE SUCCÈS
            ================================================== --}}

            <div
                id="loginSuccess"
                class="alert alert-success d-none"
                role="alert">
            </div>


            {{-- ==================================================
                 FORMULAIRE
            ================================================== --}}

            <form id="loginForm">

                {{-- Email --}}

                <div class="mb-3">

                    <label
                        for="email"
                        class="form-label fw-semibold"
                    >
                        Adresse email
                    </label>

                    <input
                        type="email"
                        class="form-control form-control-lg"
                        id="email"
                        name="email"
                        placeholder="exemple@email.com"
                        required
                    >

                </div>


                {{-- Password --}}

                <div class="mb-3">

                    <label
                        for="password"
                        class="form-label fw-semibold"
                    >
                        Mot de passe
                    </label>

                    <input
                        type="password"
                        class="form-control form-control-lg"
                        id="password"
                        name="password"
                        placeholder="Votre mot de passe"
                        required
                    >

                </div>


                {{-- Remember me --}}

                <div class="form-check mb-4">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        id="remember"
                        name="remember"
                    >

                    <label
                        class="form-check-label"
                        for="remember"
                    >
                        Se souvenir de moi
                    </label>

                </div>


                {{-- Bouton --}}

                <div class="d-grid">

                    <button
                        type="submit"
                        id="loginButton"
                        class="btn btn-dark btn-lg"
                    >
                        Se connecter
                    </button>

                </div>

            </form>


            {{-- ==================================================
                 INSCRIPTION
            ================================================== --}}

            <div class="text-center mt-4">

                <p class="mb-0 text-muted">
                    Vous n'avez pas encore de compte ?
                </p>

                <a
                    href="/register"
                    class="text-decoration-none fw-semibold"
                >
                    Créer un compte
                </a>

            </div>

        </div>

    </div>

</div>
```

</div>

<script>

/*
==========================================================
LOGIN MARKETCYLIA
==========================================================
*/


/*
==========================================================
ÉLÉMENTS
==========================================================
*/

const loginForm =
    document.getElementById('loginForm');

const loginButton =
    document.getElementById('loginButton');

const loginError =
    document.getElementById('loginError');

const loginSuccess =
    document.getElementById('loginSuccess');


/*
==========================================================
SOUMISSION DU FORMULAIRE
==========================================================
*/

loginForm.addEventListener(
    'submit',
    async function(event) {

        /*
        --------------------------------------------------
        Empêcher le rechargement
        --------------------------------------------------
        */

        event.preventDefault();


        /*
        --------------------------------------------------
        Masquer les anciens messages
        --------------------------------------------------
        */

        loginError.classList.add('d-none');

        loginSuccess.classList.add('d-none');


        /*
        --------------------------------------------------
        Récupérer les valeurs
        --------------------------------------------------
        */

        const email =
            document
                .getElementById('email')
                .value
                .trim();

        const password =
            document
                .getElementById('password')
                .value;


        /*
        --------------------------------------------------
        Désactiver le bouton
        --------------------------------------------------
        */

        loginButton.disabled = true;

        loginButton.textContent =
            'Connexion...';


        try {

            /*
            ==================================================
            IMPORTANT
            ==================================================

            Supprimer les anciennes informations AVANT
            de commencer une nouvelle connexion.

            Cela empêche un ancien compte vendeur
            de rester dans le navigateur.
            ==================================================
            */

            localStorage.removeItem(
                'marketcylia_token'
            );

            localStorage.removeItem(
                'marketcylia_user'
            );


            /*
            --------------------------------------------------
            Appel API LOGIN
            --------------------------------------------------
            */

            const response =
                await fetch(
                    'http://127.0.0.1:8000/api/login',
                    {
                        method: 'POST',

                        headers: {
                            'Content-Type':
                                'application/json',

                            'Accept':
                                'application/json'
                        },

                        body: JSON.stringify({

                            email: email,

                            password: password

                        })
                    }
                );


            /*
            --------------------------------------------------
            Lire la réponse
            --------------------------------------------------
            */

            const data =
                await response.json();


            /*
            --------------------------------------------------
            Vérifier la réponse
            --------------------------------------------------
            */

            if (!response.ok) {

                if (
                    data.errors &&
                    data.errors.email
                ) {

                    throw new Error(
                        data.errors.email[0]
                    );

                }

                throw new Error(
                    data.message ||
                    'Erreur lors de la connexion.'
                );

            }


            /*
            ==================================================
            CONNEXION RÉUSSIE
            ==================================================
            */

            console.log(
                'Connexion réussie :',
                data.user
            );


            console.log(
                'Token reçu :',
                data.token
            );


            /*
            ==================================================
            VÉRIFICATION
            ==================================================
            */

            if (!data.user) {

                throw new Error(
                    'Les informations utilisateur sont absentes.'
                );

            }


            if (!data.token) {

                throw new Error(
                    'Le token d’authentification est absent.'
                );

            }


            /*
            ==================================================
            SAUVEGARDER LE NOUVEAU TOKEN
            ==================================================
            */

            localStorage.setItem(
                'marketcylia_token',
                data.token
            );


            /*
            ==================================================
            SAUVEGARDER LE NOUVEL UTILISATEUR
            ==================================================
            */

            localStorage.setItem(
                'marketcylia_user',
                JSON.stringify(data.user)
            );


            /*
            ==================================================
            VÉRIFICATION DU STOCKAGE
            ==================================================
            */

            console.log(
                'TOKEN STOCKÉ :',
                localStorage.getItem(
                    'marketcylia_token'
                )
            );


            console.log(
                'USER STOCKÉ :',
                localStorage.getItem(
                    'marketcylia_user'
                )
            );


            /*
            ==================================================
            AFFICHER LE RÔLE
            ==================================================
            */

            const roleName =
                data.user.role?.name ||
                data.user.role ||
                'Utilisateur';


            console.log(
                'Utilisateur connecté :',
                data.user.name
            );


            console.log(
                'Rôle :',
                roleName
            );


            /*
            ==================================================
            MESSAGE DE SUCCÈS
            ==================================================
            */

            loginSuccess.textContent =
                `Bienvenue ${data.user.name} ! Redirection...`;

            loginSuccess.classList.remove(
                'd-none'
            );


            /*
            ==================================================
            REDIRECTION
            ==================================================
            */

            setTimeout(
                function() {

                    window.location.href = '/';

                },
                700
            );


        } catch (error) {

            /*
            ==================================================
            ERREUR
            ==================================================
            */

            console.error(
                'Erreur de connexion :',
                error
            );


            loginError.textContent =
                error.message ||
                'Une erreur est survenue.';


            loginError.classList.remove(
                'd-none'
            );


        } finally {

            /*
            ==================================================
            RÉACTIVER LE BOUTON
            ==================================================
            */

            loginButton.disabled = false;

            loginButton.textContent =
                'Se connecter';

        }

    }
);

</script>

@endsection
