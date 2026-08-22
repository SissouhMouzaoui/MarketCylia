<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'MarketCylia')
    </title>


    {{-- ==========================================================
         BOOTSTRAP 5
    ========================================================== --}}

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

</head>


<body class="bg-light">


{{-- ==============================================================
     NAVBAR
================================================================= --}}

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">

    <div class="container">


        {{-- ======================================================
             LOGO
        ======================================================= --}}

        <a
            class="navbar-brand fw-bold"
            href="/"
        >
            MarketCylia
        </a>


        {{-- ======================================================
             MOBILE BUTTON
        ======================================================= --}}

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarContent"
            aria-controls="navbarContent"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >

            <span class="navbar-toggler-icon"></span>

        </button>


        {{-- ======================================================
             NAVIGATION
        ======================================================= --}}

        <div
            class="collapse navbar-collapse"
            id="navbarContent"
        >

            <ul class="navbar-nav ms-auto align-items-lg-center">


                {{-- ==================================================
                     ACCUEIL
                ================================================== --}}

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="/"
                    >
                        Accueil
                    </a>

                </li>


                {{-- ==================================================
                     PRODUITS
                ================================================== --}}

                <li class="nav-item">

                    <a
                        class="nav-link"
                        href="/products"
                    >
                        Produits
                    </a>

                </li>


                {{-- ==================================================
                     MES COMMANDES
                     Visible pour tout utilisateur connecté
                ================================================== --}}

                <li
                    class="nav-item d-none"
                    id="ordersMenuItem"
                >

                    <a
                        class="nav-link"
                        href="/orders"
                    >
                        📦 Mes commandes
                    </a>

                </li>


                {{-- ==================================================
                     COMMANDES VENDEUR
                     Visible uniquement pour Seller
                ================================================== --}}

                <li
                    class="nav-item d-none"
                    id="sellerOrdersMenuItem"
                >

                    <a
                        class="nav-link"
                        href="/seller/orders"
                    >
                        🏪 Commandes
                    </a>

                </li>


                {{-- ==================================================
                     CONNEXION
                ================================================== --}}

                <li
                    class="nav-item"
                    id="guestLogin"
                >

                    <a
                        class="nav-link"
                        href="/login"
                    >
                        Connexion
                    </a>

                </li>


                {{-- ==================================================
                     INSCRIPTION
                ================================================== --}}

                <li
                    class="nav-item"
                    id="guestRegister"
                >

                    <a
                        class="nav-link"
                        href="/register"
                    >
                        Inscription
                    </a>

                </li>


                {{-- ==================================================
                     MENU UTILISATEUR CONNECTÉ
                ================================================== --}}

                <li
                    class="nav-item dropdown d-none"
                    id="userMenu"
                >

                    <a
                        class="nav-link dropdown-toggle"
                        href="#"
                        role="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >

                        Bonjour,
                        <strong id="navbarUserName"></strong>

                    </a>


                    {{-- ==================================================
                         DROPDOWN
                    ================================================== --}}

                    <ul
                        class="dropdown-menu dropdown-menu-end"
                    >


                        {{-- ==================================================
                             ESPACE VENDEUR
                        ================================================== --}}

                        <li
                            id="sellerMenuItem"
                            class="d-none"
                        >

                            <a
                                class="dropdown-item"
                                href="/seller/dashboard"
                            >
                                🏪 Espace vendeur
                            </a>

                        </li>


                        {{-- ==================================================
                             SÉPARATEUR
                        ================================================== --}}

                        <li
                            id="sellerDivider"
                            class="d-none"
                        >

                            <hr class="dropdown-divider">

                        </li>


                        {{-- ==================================================
                             DÉCONNEXION
                        ================================================== --}}

                        <li>

                            <button
                                type="button"
                                id="logoutButton"
                                class="dropdown-item text-danger"
                            >

                                🚪 Déconnexion

                            </button>

                        </li>


                    </ul>

                </li>

            </ul>

        </div>

    </div>

</nav>



{{-- ==============================================================
     MESSAGE NAVBAR
================================================================= --}}

<div class="container">

    <div
        id="navbarMessage"
        class="alert d-none mt-3"
    ></div>

</div>



{{-- ==============================================================
     CONTENU
================================================================= --}}

<main class="container py-4">

    @yield('content')

</main>



{{-- ==============================================================
     FOOTER
================================================================= --}}

<footer class="bg-dark text-white mt-5 py-4">

    <div class="container text-center">

        <p class="mb-1">

            &copy; {{ date('Y') }} MarketCylia

        </p>

        <small>

            Marketplace multi-vendeurs

        </small>

    </div>

</footer>



{{-- ==============================================================
     BOOTSTRAP JAVASCRIPT
================================================================= --}}

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
</script>



{{-- ==============================================================
     AUTHENTIFICATION
================================================================= --}}

<script>


/*
==============================================================
ÉLÉMENTS DOM
==============================================================
*/

const navbarUserName =
    document.getElementById(
        'navbarUserName'
    );


const userMenu =
    document.getElementById(
        'userMenu'
    );


const guestLogin =
    document.getElementById(
        'guestLogin'
    );


const guestRegister =
    document.getElementById(
        'guestRegister'
    );


const ordersMenuItem =
    document.getElementById(
        'ordersMenuItem'
    );


const sellerOrdersMenuItem =
    document.getElementById(
        'sellerOrdersMenuItem'
    );


const sellerMenuItem =
    document.getElementById(
        'sellerMenuItem'
    );


const sellerDivider =
    document.getElementById(
        'sellerDivider'
    );


const logoutButton =
    document.getElementById(
        'logoutButton'
    );


const navbarMessage =
    document.getElementById(
        'navbarMessage'
    );



/*
==============================================================
AFFICHER UN MESSAGE
==============================================================
*/

function showNavbarMessage(
    message,
    type = 'danger'
) {

    navbarMessage.textContent =
        message;


    navbarMessage.className =
        `alert alert-${type} mt-3`;

}



/*
==============================================================
AFFICHER UTILISATEUR CONNECTÉ
==============================================================
*/

function showAuthenticatedUser(
    user
) {


    /*
    ----------------------------------------------------------
    NOM UTILISATEUR
    ----------------------------------------------------------
    */

    navbarUserName.textContent =
        user.name ||
        'Utilisateur';



    /*
    ----------------------------------------------------------
    AFFICHER MENU UTILISATEUR
    ----------------------------------------------------------
    */

    userMenu.classList.remove(
        'd-none'
    );



    /*
    ----------------------------------------------------------
    CACHER CONNEXION
    ----------------------------------------------------------
    */

    guestLogin.classList.add(
        'd-none'
    );



    /*
    ----------------------------------------------------------
    CACHER INSCRIPTION
    ----------------------------------------------------------
    */

    guestRegister.classList.add(
        'd-none'
    );



    /*
    ----------------------------------------------------------
    AFFICHER MES COMMANDES
    ----------------------------------------------------------
    */

    ordersMenuItem.classList.remove(
        'd-none'
    );



    /*
    ==========================================================
    RÉCUPÉRER LE RÔLE
    ==========================================================
    */

    let roleName = '';


    if (
        user.role &&
        typeof user.role === 'object'
    ) {

        roleName =
            user.role.name ||
            '';

    } else {

        roleName =
            user.role ||
            '';

    }


    /*
    ----------------------------------------------------------
    Normaliser le rôle
    ----------------------------------------------------------
    */

    roleName =
        String(roleName)
            .trim()
            .toLowerCase();



    /*
    ----------------------------------------------------------
    DEBUG
    ----------------------------------------------------------
    */

    console.log(
        'Rôle utilisateur :',
        roleName
    );



    /*
    ==========================================================
    VENDEUR
    ==========================================================
    */

    if (
        roleName === 'seller' ||
        roleName === 'vendeur'
    ) {


        /*
        ------------------------------------------------------
        Espace vendeur
        ------------------------------------------------------
        */

        sellerMenuItem.classList.remove(
            'd-none'
        );


        /*
        ------------------------------------------------------
        Séparateur
        ------------------------------------------------------
        */

        sellerDivider.classList.remove(
            'd-none'
        );


        /*
        ------------------------------------------------------
        Commandes vendeur
        ------------------------------------------------------
        */

        sellerOrdersMenuItem.classList.remove(
            'd-none'
        );

    }

}



/*
==============================================================
VÉRIFIER AUTHENTIFICATION
==============================================================
*/

function checkAuthentication() {


    /*
    ----------------------------------------------------------
    Récupérer utilisateur
    ----------------------------------------------------------
    */

    const storedUser =
        localStorage.getItem(
            'marketcylia_user'
        );



    /*
    ----------------------------------------------------------
    Aucun utilisateur
    ----------------------------------------------------------
    */

    if (!storedUser) {

        return;

    }



    try {


        /*
        ------------------------------------------------------
        Convertir JSON
        ------------------------------------------------------
        */

        const user =
            JSON.parse(
                storedUser
            );



        /*
        ------------------------------------------------------
        Afficher utilisateur
        ------------------------------------------------------
        */

        showAuthenticatedUser(
            user
        );


    } catch (error) {


        console.error(
            'Erreur lecture utilisateur :',
            error
        );


        /*
        ------------------------------------------------------
        Nettoyer stockage corrompu
        ------------------------------------------------------
        */

        localStorage.removeItem(
            'marketcylia_user'
        );


        localStorage.removeItem(
            'marketcylia_token'
        );

    }

}



/*
==============================================================
DÉCONNEXION
==============================================================
*/

logoutButton.addEventListener(
    'click',
    async function() {


        /*
        ------------------------------------------------------
        Désactiver bouton
        ------------------------------------------------------
        */

        logoutButton.disabled =
            true;


        logoutButton.textContent =
            'Déconnexion...';



        /*
        ------------------------------------------------------
        TOKEN
        ------------------------------------------------------
        */

        const token =
            localStorage.getItem(
                'marketcylia_token'
            );



        /*
        ------------------------------------------------------
        Aucun token
        ------------------------------------------------------
        */

        if (!token) {

            window.location.href =
                '/';

            return;

        }



        try {


            /*
            --------------------------------------------------
            Déconnexion Laravel
            --------------------------------------------------
            */

            const response =
                await fetch(
                    'http://127.0.0.1:8000/api/logout',
                    {
                        method: 'POST',

                        headers: {

                            'Accept':
                                'application/json',

                            'Authorization':
                                'Bearer ' +
                                token

                        }

                    }
                );


            console.log(
                'Réponse déconnexion :',
                response.status
            );


        } catch (error) {


            /*
            --------------------------------------------------
            Même si API indisponible,
            déconnexion locale
            --------------------------------------------------
            */

            console.error(
                'Erreur API déconnexion :',
                error
            );

        }



        /*
        ------------------------------------------------------
        Supprimer token
        ------------------------------------------------------
        */

        localStorage.removeItem(
            'marketcylia_token'
        );


        /*
        ------------------------------------------------------
        Supprimer utilisateur
        ------------------------------------------------------
        */

        localStorage.removeItem(
            'marketcylia_user'
        );



        /*
        ------------------------------------------------------
        Retour accueil
        ------------------------------------------------------
        */

        window.location.href =
            '/';

    }
);



/*
==============================================================
DÉMARRAGE
==============================================================
*/

checkAuthentication();


</script>


</body>

</html>
