@extends('layouts.app')

@section('title', 'Créer un compte - MarketCylia')

@section('content')

<div class="row justify-content-center">

```
<div class="col-md-8 col-lg-6">

    {{-- ==================================================
         EN-TÊTE
    ================================================== --}}

    <div class="text-center mb-4">

        <h1 class="fw-bold">
            Créer un compte
        </h1>

        <p class="text-muted">
            Rejoignez MarketCylia.
        </p>

    </div>


    {{-- ==================================================
         ERREUR
    ================================================== --}}

    <div
        id="registerError"
        class="alert alert-danger d-none">
    </div>


    {{-- ==================================================
         SUCCÈS
    ================================================== --}}

    <div
        id="registerSuccess"
        class="alert alert-success d-none">
    </div>


    {{-- ==================================================
         FORMULAIRE
    ================================================== --}}

    <div class="card border-0 shadow-sm">

        <div class="card-body p-4 p-md-5">

            <form id="registerForm">

                {{-- ==================================================
                     TYPE DE COMPTE
                ================================================== --}}

                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Quel type de compte souhaitez-vous créer ?
                    </label>


                    {{-- Acheteur --}}

                    <div
                        class="form-check border rounded p-3 mb-2">

                        <input
                            class="form-check-input"
                            type="radio"
                            name="account_type"
                            id="accountCustomer"
                            value="customer"
                            checked
                        >

                        <label
                            class="form-check-label w-100"
                            for="accountCustomer">

                            <strong>
                                🛍️ Acheteur
                            </strong>

                            <div class="text-muted small mt-1">
                                Je souhaite acheter des produits.
                            </div>

                        </label>

                    </div>


                    {{-- Vendeur --}}

                    <div
                        class="form-check border rounded p-3">

                        <input
                            class="form-check-input"
                            type="radio"
                            name="account_type"
                            id="accountSeller"
                            value="seller"
                        >

                        <label
                            class="form-check-label w-100"
                            for="accountSeller">

                            <strong>
                                🏪 Vendeur
                            </strong>

                            <div class="text-muted small mt-1">
                                Je souhaite vendre mes produits sur MarketCylia.
                            </div>

                        </label>

                    </div>

                </div>


                {{-- ==================================================
                     NOM
                ================================================== --}}

                <div class="mb-4">

                    <label
                        for="name"
                        class="form-label fw-semibold">

                        Nom complet

                    </label>

                    <input
                        type="text"
                        id="name"
                        class="form-control form-control-lg"
                        placeholder="Votre nom complet"
                        required
                    >

                </div>


                {{-- ==================================================
                     EMAIL
                ================================================== --}}

                <div class="mb-4">

                    <label
                        for="email"
                        class="form-label fw-semibold">

                        Adresse email

                    </label>

                    <input
                        type="email"
                        id="email"
                        class="form-control form-control-lg"
                        placeholder="exemple@email.com"
                        required
                    >

                </div>


                {{-- ==================================================
                     MOT DE PASSE
                ================================================== --}}

                <div class="mb-4">

                    <label
                        for="password"
                        class="form-label fw-semibold">

                        Mot de passe

                    </label>

                    <input
                        type="password"
                        id="password"
                        class="form-control form-control-lg"
                        placeholder="Votre mot de passe"
                        required
                    >

                </div>


                {{-- ==================================================
                     CONFIRMATION
                ================================================== --}}

                <div class="mb-4">

                    <label
                        for="password_confirmation"
                        class="form-label fw-semibold">

                        Confirmer le mot de passe

                    </label>

                    <input
                        type="password"
                        id="password_confirmation"
                        class="form-control form-control-lg"
                        placeholder="Confirmez votre mot de passe"
                        required
                    >

                </div>


                {{-- ==================================================
                     BOUTON
                ================================================== --}}

                <button
                    type="submit"
                    id="registerButton"
                    class="btn btn-dark btn-lg w-100">

                    Créer mon compte

                </button>

            </form>


            {{-- ==================================================
                 CONNEXION
            ================================================== --}}

            <div class="text-center mt-4">

                <span class="text-muted">
                    Vous avez déjà un compte ?
                </span>

                <a
                    href="/login"
                    class="fw-semibold text-decoration-none">

                    Se connecter

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
INSCRIPTION
==========================================================
*/


const registerForm =
    document.getElementById('registerForm');

const registerButton =
    document.getElementById('registerButton');

const registerError =
    document.getElementById('registerError');

const registerSuccess =
    document.getElementById('registerSuccess');


/*
==========================================================
SOUMISSION
==========================================================
*/

registerForm.addEventListener(
    'submit',
    async function(event) {

        event.preventDefault();


        registerError.classList.add(
            'd-none'
        );

        registerSuccess.classList.add(
            'd-none'
        );


        /*
        --------------------------------------------------
        Récupérer les informations
        --------------------------------------------------
        */

        const name =
            document
                .getElementById('name')
                .value
                .trim();


        const email =
            document
                .getElementById('email')
                .value
                .trim();


        const password =
            document
                .getElementById('password')
                .value;


        const passwordConfirmation =
            document
                .getElementById('password_confirmation')
                .value;


        /*
        --------------------------------------------------
        Type de compte
        --------------------------------------------------
        */

        const accountType =
            document.querySelector(
                'input[name="account_type"]:checked'
            ).value;


        /*
        --------------------------------------------------
        Vérification mot de passe
        --------------------------------------------------
        */

        if (
            password !==
            passwordConfirmation
        ) {

            registerError.textContent =
                'Les mots de passe ne correspondent pas.';

            registerError.classList.remove(
                'd-none'
            );

            return;

        }


        /*
        --------------------------------------------------
        Désactiver le bouton
        --------------------------------------------------
        */

        registerButton.disabled =
            true;

        registerButton.textContent =
            'Création du compte...';


        try {

            /*
            --------------------------------------------------
            Appel API
            --------------------------------------------------
            */

            const response =
                await fetch(
                    'http://127.0.0.1:8000/api/register',
                    {
                        method: 'POST',

                        headers: {

                            'Content-Type':
                                'application/json',

                            'Accept':
                                'application/json'

                        },

                        body: JSON.stringify({

                            name:
                                name,

                            email:
                                email,

                            password:
                                password,

                            password_confirmation:
                                passwordConfirmation,

                            account_type:
                                accountType

                        })

                    }
                );


            const data =
                await response.json();


            /*
            --------------------------------------------------
            Vérifier réponse
            --------------------------------------------------
            */

            if (!response.ok) {

                if (data.errors) {

                    const errors =
                        Object
                            .values(data.errors)
                            .flat()
                            .join(' ');

                    throw new Error(
                        errors
                    );

                }


                throw new Error(
                    data.message ||
                    'Impossible de créer le compte.'
                );

            }


            /*
            --------------------------------------------------
            Sauvegarder le token
            --------------------------------------------------
            */

            if (data.token) {

                localStorage.setItem(
                    'marketcylia_token',
                    data.token
                );

            }


            /*
            --------------------------------------------------
            Succès
            --------------------------------------------------
            */

            registerSuccess.textContent =
                data.message ||
                'Inscription réussie.';


            registerSuccess.classList.remove(
                'd-none'
            );


            /*
            --------------------------------------------------
            Redirection selon le rôle
            --------------------------------------------------
            */

            setTimeout(
                function() {

                    if (
                        data.user &&
                        data.user.role &&
                        data.user.role.name === 'Seller'
                    ) {

                        window.location.href =
                            '/seller/dashboard';

                    } else {

                        window.location.href =
                            '/';

                    }

                },
                1200
            );


        } catch (error) {

            console.error(
                'Erreur inscription :',
                error
            );


            registerError.textContent =
                error.message ||
                'Une erreur est survenue.';


            registerError.classList.remove(
                'd-none'
            );


        } finally {

            registerButton.disabled =
                false;

            registerButton.textContent =
                'Créer mon compte';

        }

    }
);

</script>

@endsection
