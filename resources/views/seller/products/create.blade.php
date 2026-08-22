@extends('layouts.app')

@section('title', 'Ajouter un produit - MarketCylia')

@section('content')

<div class="row justify-content-center">

    <div class="col-lg-8">

        {{-- ==========================================================
             EN-TÊTE
        ========================================================== --}}

        <div class="mb-4">

            <h1 class="fw-bold">
                Ajouter un produit
            </h1>

            <p class="text-muted">
                Ajoutez un nouveau produit à votre boutique.
            </p>

        </div>


        {{-- ==========================================================
             FORMULAIRE
        ========================================================== --}}

        <div class="card border-0 shadow-sm">

            <div class="card-body p-4 p-md-5">

                {{-- Message d'erreur --}}
                <div
                    id="productError"
                    class="alert alert-danger d-none">
                </div>


                {{-- Message de succès --}}
                <div
                    id="productSuccess"
                    class="alert alert-success d-none">
                </div>


                <form id="productForm">

                    {{-- ==================================================
                         NOM
                    ================================================== --}}

                    <div class="mb-4">

                        <label
                            for="name"
                            class="form-label fw-semibold">

                            Nom du produit

                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            class="form-control form-control-lg"
                            placeholder="Exemple : Sac à main"
                            required
                        >

                    </div>


                    {{-- ==================================================
                         DESCRIPTION
                    ================================================== --}}

                    <div class="mb-4">

                        <label
                            for="description"
                            class="form-label fw-semibold">

                            Description

                        </label>

                        <textarea
                            id="description"
                            name="description"
                            class="form-control"
                            rows="5"
                            placeholder="Décrivez votre produit..."
                        ></textarea>

                    </div>


                    {{-- ==================================================
                         CATÉGORIE
                    ================================================== --}}

                    <div class="mb-4">

                        <label
                            for="category_id"
                            class="form-label fw-semibold">

                            Type de produit

                        </label>

                        <select
                            id="category_id"
                            name="category_id"
                            class="form-select form-select-lg"
                            required
                        >

                            <option value="">
                                Chargement des catégories...
                            </option>

                        </select>

                        <div class="form-text">

                            Choisissez la catégorie correspondant
                            à votre produit.

                        </div>

                    </div>


                    {{-- ==================================================
                         PRIX + STOCK
                    ================================================== --}}

                    <div class="row">

                        {{-- Prix --}}
                        <div class="col-md-6">

                            <div class="mb-4">

                                <label
                                    for="price"
                                    class="form-label fw-semibold">

                                    Prix (DA)

                                </label>

                                <input
                                    type="number"
                                    id="price"
                                    name="price"
                                    class="form-control form-control-lg"
                                    min="0"
                                    step="0.01"
                                    placeholder="2500"
                                    required
                                >

                            </div>

                        </div>


                        {{-- Stock --}}
                        <div class="col-md-6">

                            <div class="mb-4">

                                <label
                                    for="stock"
                                    class="form-label fw-semibold">

                                    Stock

                                </label>

                                <input
                                    type="number"
                                    id="stock"
                                    name="stock"
                                    class="form-control form-control-lg"
                                    min="0"
                                    step="1"
                                    placeholder="10"
                                    required
                                >

                            </div>

                        </div>

                    </div>


                    {{-- ==================================================
                         IMAGES
                    ================================================== --}}

                    <div class="mb-4">

                        <label
                            for="images"
                            class="form-label fw-semibold">

                            Images du produit

                        </label>

                        <input
                            type="file"
                            id="images"
                            name="images[]"
                            class="form-control"
                            accept="image/jpeg,image/png,image/webp"
                            multiple
                        >

                        <div class="form-text">

                            Vous pouvez sélectionner jusqu'à 8 images.
                            Formats acceptés : JPG, PNG et WebP.

                        </div>

                    </div>


                    {{-- ==================================================
                         PRÉVISUALISATION
                    ================================================== --}}

                    <div
                        id="imagePreview"
                        class="row g-3 mb-4">
                    </div>


                    {{-- ==================================================
                         PRODUIT ACTIF
                    ================================================== --}}

                    <div class="form-check mb-4">

                        <input
                            type="checkbox"
                            class="form-check-input"
                            id="is_active"
                            name="is_active"
                            checked
                        >

                        <label
                            class="form-check-label"
                            for="is_active">

                            Publier ce produit

                        </label>

                    </div>


                    {{-- ==================================================
                         BOUTONS
                    ================================================== --}}

                    <div class="d-flex gap-2">

                        <a
                            href="/seller/dashboard"
                            class="btn btn-outline-secondary">

                            Annuler

                        </a>

                        <button
                            type="submit"
                            id="saveProductButton"
                            class="btn btn-dark">

                            Ajouter le produit

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>


<script>

/*
==========================================================
AJOUT D'UN PRODUIT
==========================================================
*/


/*
==========================================================
ÉLÉMENTS
==========================================================
*/

const productForm =
    document.getElementById('productForm');

const saveProductButton =
    document.getElementById('saveProductButton');

const productError =
    document.getElementById('productError');

const productSuccess =
    document.getElementById('productSuccess');

const imageInput =
    document.getElementById('images');

const imagePreview =
    document.getElementById('imagePreview');

const categorySelect =
    document.getElementById('category_id');


/*
==========================================================
TOKEN
==========================================================
*/

const productToken =
    localStorage.getItem('marketcylia_token');


/*
==========================================================
VÉRIFICATION DU TOKEN
==========================================================
*/

if (!productToken) {

    window.location.href = '/login';

}


/*
==========================================================
PRÉVISUALISATION DES IMAGES
==========================================================
*/

imageInput.addEventListener(
    'change',
    function() {

        imagePreview.innerHTML = '';

        const files =
            Array.from(imageInput.files);


        /*
        --------------------------------------------------
        Vérifier le nombre d'images
        --------------------------------------------------
        */

        if (files.length > 8) {

            imageInput.value = '';

            productError.textContent =
                'Vous pouvez sélectionner au maximum 8 images.';

            productError.classList.remove(
                'd-none'
            );

            return;
        }


        /*
        --------------------------------------------------
        Masquer l'erreur
        --------------------------------------------------
        */

        productError.classList.add(
            'd-none'
        );


        /*
        --------------------------------------------------
        Afficher les images
        --------------------------------------------------
        */

        files.forEach(
            function(file, index) {

                const reader =
                    new FileReader();


                reader.onload =
                    function(event) {

                        const column =
                            document.createElement('div');

                        column.className =
                            'col-6 col-md-3';


                        column.innerHTML = `

                            <div class="card border-0 shadow-sm">

                                <img
                                    src="${event.target.result}"
                                    class="card-img-top"
                                    style="
                                        height: 160px;
                                        object-fit: cover;
                                    "
                                    alt="Image ${index + 1}"
                                >

                                <div class="card-body p-2 text-center">

                                    ${
                                        index === 0
                                        ?
                                        '<span class="badge text-bg-dark">Image principale</span>'
                                        :
                                        '<span class="badge text-bg-light">Image ' +
                                        (index + 1) +
                                        '</span>'
                                    }

                                </div>

                            </div>

                        `;


                        imagePreview.appendChild(
                            column
                        );

                    };


                reader.readAsDataURL(file);

            }
        );

    }
);


/*
==========================================================
CHARGER LES CATÉGORIES
==========================================================
*/

async function loadCategories() {

    try {

        const response =
            await fetch(
                'http://127.0.0.1:8000/api/categories',
                {
                    method: 'GET',

                    headers: {
                        'Accept':
                            'application/json'
                    }
                }
            );


        /*
        --------------------------------------------------
        Vérification de la réponse
        --------------------------------------------------
        */

        if (!response.ok) {

            throw new Error(
                'Impossible de charger les catégories.'
            );

        }


        /*
        --------------------------------------------------
        Lire la réponse JSON
        --------------------------------------------------
        */

        const data =
            await response.json();


        /*
        --------------------------------------------------
        Support de plusieurs formats de réponse
        --------------------------------------------------
        */

        const categories =
            Array.isArray(data)
                ? data
                : (data.categories || []);


        /*
        --------------------------------------------------
        Vérifier qu'il existe des catégories
        --------------------------------------------------
        */

        if (categories.length === 0) {

            categorySelect.innerHTML = `

                <option value="">
                    Aucune catégorie disponible
                </option>

            `;

            return;
        }


        /*
        --------------------------------------------------
        Réinitialiser la liste
        --------------------------------------------------
        */

        categorySelect.innerHTML = `

            <option value="">
                Sélectionnez une catégorie
            </option>

        `;


        /*
        --------------------------------------------------
        Ajouter les catégories
        --------------------------------------------------
        */

        categories.forEach(
            function(category) {

                const option =
                    document.createElement('option');


                option.value =
                    category.id;


                option.textContent =
                    category.name;


                categorySelect.appendChild(
                    option
                );

            }
        );


    } catch (error) {

        console.error(
            'Erreur chargement catégories :',
            error
        );


        categorySelect.innerHTML = `

            <option value="">
                Impossible de charger les catégories
            </option>

        `;


        productError.textContent =
            error.message ||
            'Impossible de charger les catégories.';


        productError.classList.remove(
            'd-none'
        );

    }

}


/*
==========================================================
SOUMISSION DU FORMULAIRE
==========================================================
*/

productForm.addEventListener(
    'submit',
    async function(event) {

        event.preventDefault();


        /*
        --------------------------------------------------
        Masquer les anciens messages
        --------------------------------------------------
        */

        productError.classList.add(
            'd-none'
        );

        productSuccess.classList.add(
            'd-none'
        );


        /*
        --------------------------------------------------
        Récupérer les valeurs
        --------------------------------------------------
        */

        const name =
            document
                .getElementById('name')
                .value
                .trim();


        const description =
            document
                .getElementById('description')
                .value
                .trim();


        const price =
            document
                .getElementById('price')
                .value;


        const stock =
            document
                .getElementById('stock')
                .value;


        const categoryId =
            document
                .getElementById('category_id')
                .value;


        const isActive =
            document
                .getElementById('is_active')
                .checked;


        /*
        --------------------------------------------------
        Vérifier la catégorie
        --------------------------------------------------
        */

        if (!categoryId) {

            productError.textContent =
                'Veuillez sélectionner une catégorie.';

            productError.classList.remove(
                'd-none'
            );

            return;
        }


        /*
        ==================================================
        FORMDATA
        ==================================================
        */

        const formData =
            new FormData();


        formData.append(
            'name',
            name
        );


        formData.append(
            'description',
            description
        );


        formData.append(
            'price',
            price
        );


        formData.append(
            'stock',
            stock
        );


        formData.append(
            'category_id',
            categoryId
        );


        formData.append(
            'is_active',
            isActive ? '1' : '0'
        );


        /*
        --------------------------------------------------
        Ajouter les images
        --------------------------------------------------
        */

        const selectedImages =
            imageInput.files;


        for (
            let i = 0;
            i < selectedImages.length;
            i++
        ) {

            formData.append(
                'images[]',
                selectedImages[i]
            );

        }


        /*
        ==================================================
        DÉSACTIVER LE BOUTON
        ==================================================
        */

        saveProductButton.disabled =
            true;


        saveProductButton.textContent =
            'Enregistrement...';


        /*
        ==================================================
        ENVOI VERS LARAVEL
        ==================================================
        */

        try {

            const response =
                await fetch(
                    'http://127.0.0.1:8000/api/seller/products',
                    {
                        method: 'POST',

                        headers: {

                            'Accept':
                                'application/json',

                            'Authorization':
                                'Bearer ' +
                                productToken

                        },

                        body:
                            formData

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
            ==================================================
            ERREUR
            ==================================================
            */

            if (!response.ok) {

                if (data.errors) {

                    const errors =
                        Object.values(
                            data.errors
                        )
                        .flat()
                        .join(' ');


                    throw new Error(
                        errors
                    );

                }


                throw new Error(
                    data.message ||
                    'Impossible de créer le produit.'
                );

            }


            /*
            ==================================================
            SUCCÈS
            ==================================================
            */

            console.log(
                'Produit créé :',
                data.product
            );


            productSuccess.textContent =
                'Produit créé avec succès !';


            productSuccess.classList.remove(
                'd-none'
            );


            /*
            --------------------------------------------------
            Redirection
            --------------------------------------------------
            */

            setTimeout(
                function() {

                    window.location.href =
                        '/seller/dashboard';

                },
                1000
            );


        } catch (error) {

            console.error(
                'Erreur création produit :',
                error
            );


            productError.textContent =
                error.message ||
                'Une erreur est survenue.';


            productError.classList.remove(
                'd-none'
            );


        } finally {

            saveProductButton.disabled =
                false;


            saveProductButton.textContent =
                'Ajouter le produit';

        }

    }
);


/*
==========================================================
DÉMARRAGE
==========================================================
*/

loadCategories();

</script>

@endsection
