@extends('layouts.app')

@section('title', 'Modifier un produit - MarketCylia')

@section('content')

<div class="row justify-content-center">

```
<div class="col-lg-8">

    <div class="mb-4">

        <h1 class="fw-bold">
            Modifier le produit
        </h1>

        <p class="text-muted">
            Modifiez les informations de votre produit.
        </p>

    </div>


    {{-- Erreur --}}
    <div
        id="productError"
        class="alert alert-danger d-none">
    </div>


    {{-- Succès --}}
    <div
        id="productSuccess"
        class="alert alert-success d-none">
    </div>


    {{-- Chargement --}}
    <div
        id="loading"
        class="text-center py-5">

        <div class="spinner-border"></div>

        <p class="text-muted mt-3">
            Chargement du produit...
        </p>

    </div>


    {{-- Formulaire --}}
    <div
        id="editContainer"
        class="card border-0 shadow-sm d-none">

        <div class="card-body p-4 p-md-5">

            <form id="editProductForm">

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
                        class="form-control form-control-lg"
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
                        class="form-control"
                        rows="5"></textarea>

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
                        class="form-select form-select-lg"
                        required>

                        <option value="">
                            Chargement des catégories...
                        </option>

                    </select>

                    <div class="form-text">
                        Choisissez la catégorie correspondant à votre produit.
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
                                class="form-control form-control-lg"
                                min="0"
                                step="0.01"
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
                                class="form-control form-control-lg"
                                min="0"
                                step="1"
                                required
                            >

                        </div>

                    </div>

                </div>


                {{-- ==================================================
                     IMAGES ACTUELLES
                ================================================== --}}

                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Images du produit
                    </label>

                    <div
                        id="currentImages"
                        class="row g-3">
                    </div>

                </div>


                {{-- ==================================================
                     AJOUTER DE NOUVELLES IMAGES
                ================================================== --}}

                <div class="mb-4">

                    <label
                        for="newImages"
                        class="form-label fw-semibold">

                        Ajouter de nouvelles images

                    </label>

                    <input
                        type="file"
                        id="newImages"
                        class="form-control"
                        accept="image/jpeg,image/png,image/webp"
                        multiple
                    >

                    <div class="form-text">
                        Vous pouvez ajouter plusieurs images.
                        Maximum 8 images au total.
                    </div>

                </div>


                {{-- Prévisualisation --}}
                <div
                    id="newImagesPreview"
                    class="row g-3 mb-4">
                </div>


                {{-- ==================================================
                     STATUT
                ================================================== --}}

                <div class="form-check mb-4">

                    <input
                        type="checkbox"
                        class="form-check-input"
                        id="is_active"
                    >

                    <label
                        for="is_active"
                        class="form-check-label">

                        Produit actif

                    </label>

                </div>


                {{-- ==================================================
                     BOUTONS
                ================================================== --}}

                <div class="d-flex gap-2">

                    <a
                        href="/seller/products"
                        class="btn btn-outline-secondary">

                        Annuler

                    </a>

                    <button
                        type="submit"
                        id="updateButton"
                        class="btn btn-dark">

                        Enregistrer les modifications

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>
```

</div>

<script>

/*
==========================================================
MODIFICATION PRODUIT
==========================================================
*/

const token =
    localStorage.getItem('marketcylia_token');

const productId =
    {{ $productId }};


/*
==========================================================
ELEMENTS HTML
==========================================================
*/

const loading =
    document.getElementById('loading');

const editContainer =
    document.getElementById('editContainer');

const productError =
    document.getElementById('productError');

const productSuccess =
    document.getElementById('productSuccess');

const editProductForm =
    document.getElementById('editProductForm');

const updateButton =
    document.getElementById('updateButton');

const newImagesInput =
    document.getElementById('newImages');

const newImagesPreview =
    document.getElementById('newImagesPreview');

const currentImagesContainer =
    document.getElementById('currentImages');

const categorySelect =
    document.getElementById('category_id');


/*
==========================================================
VERIFICATION DU TOKEN
==========================================================
*/

if (!token) {

    window.location.href = '/login';

}


/*
==========================================================
AFFICHER UNE ERREUR
==========================================================
*/

function showError(message) {

    productError.textContent =
        message ||
        'Une erreur est survenue.';

    productError.classList.remove(
        'd-none'
    );

}


/*
==========================================================
MASQUER L'ERREUR
==========================================================
*/

function hideError() {

    productError.classList.add(
        'd-none'
    );

    productError.textContent = '';

}


/*
==========================================================
CHARGER LES CATÉGORIES
==========================================================
*/

async function loadCategories(
    selectedCategoryId = null
) {

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


        const data =
            await response.json();


        if (!response.ok) {

            throw new Error(
                data.message ||
                'Impossible de charger les catégories.'
            );

        }


        /*
        --------------------------------------------------
        Nettoyer le select
        --------------------------------------------------
        */

        categorySelect.innerHTML = '';


        /*
        --------------------------------------------------
        Option par défaut
        --------------------------------------------------
        */

        const defaultOption =
            document.createElement('option');

        defaultOption.value = '';

        defaultOption.textContent =
            'Sélectionnez une catégorie';

        categorySelect.appendChild(
            defaultOption
        );


        /*
        --------------------------------------------------
        Récupérer les catégories
        --------------------------------------------------
        */

        const categories =
            Array.isArray(data)
                ? data
                : data.categories || [];


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


                /*
                --------------------------------------------------
                Sélectionner la catégorie actuelle
                --------------------------------------------------
                */

                if (
                    selectedCategoryId !== null &&
                    Number(category.id) ===
                    Number(selectedCategoryId)
                ) {

                    option.selected = true;

                }


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

        showError(
            error.message
        );

    }

}


/*
==========================================================
PRÉVISUALISATION DES NOUVELLES IMAGES
==========================================================
*/

newImagesInput.addEventListener(
    'change',
    function() {

        newImagesPreview.innerHTML = '';

        const files =
            Array.from(
                newImagesInput.files
            );


        files.forEach(
            function(file, index) {

                const reader =
                    new FileReader();


                reader.onload =
                    function(event) {

                        newImagesPreview.innerHTML += `

                            <div class="col-6 col-md-3">

                                <div class="card border-0 shadow-sm">

                                    <img
                                        src="${event.target.result}"
                                        class="card-img-top"
                                        style="
                                            height: 140px;
                                            object-fit: cover;
                                        "
                                        alt="Nouvelle image ${index + 1}"
                                    >

                                    <div class="card-body p-2 text-center">

                                        <span class="badge text-bg-light">
                                            Nouvelle image ${index + 1}
                                        </span>

                                    </div>

                                </div>

                            </div>

                        `;

                    };


                reader.readAsDataURL(file);

            }
        );

    }
);


/*
==========================================================
DÉFINIR UNE IMAGE COMME PRINCIPALE
==========================================================
*/

async function setPrimaryImage(imageId) {

    try {

        hideError();


        const response =
            await fetch(
                `http://127.0.0.1:8000/api/seller/products/${productId}/images/${imageId}/primary`,
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
                "Impossible de modifier l'image principale."
            );

        }


        await loadProduct();


    } catch (error) {

        console.error(
            'Erreur image principale :',
            error
        );

        showError(
            error.message
        );

    }

}


/*
==========================================================
SUPPRESSION D'UNE IMAGE
==========================================================
*/

async function deleteProductImage(imageId) {

    const confirmed =
        confirm(
            'Êtes-vous sûr de vouloir supprimer cette image ?'
        );


    if (!confirmed) {

        return;

    }


    try {

        hideError();


        const response =
            await fetch(
                `http://127.0.0.1:8000/api/seller/products/${productId}/images/${imageId}`,
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
                'Impossible de supprimer cette image.'
            );

        }


        await loadProduct();


    } catch (error) {

        console.error(
            'Erreur suppression image :',
            error
        );

        showError(
            error.message
        );

    }

}


/*
==========================================================
CHARGER LE PRODUIT
==========================================================
*/

async function loadProduct() {

    try {

        hideError();


        const response =
            await fetch(
                `http://127.0.0.1:8000/api/seller/products/${productId}`,
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
                'Impossible de charger le produit.'
            );

        }


        const product =
            data.product;


        /*
        --------------------------------------------------
        Remplir les informations
        --------------------------------------------------
        */

        document.getElementById(
            'name'
        ).value =
            product.name ?? '';


        document.getElementById(
            'description'
        ).value =
            product.description ?? '';


        document.getElementById(
            'price'
        ).value =
            product.price ?? '';


        document.getElementById(
            'stock'
        ).value =
            product.stock ?? '';


        document.getElementById(
            'is_active'
        ).checked =
            Boolean(
                product.is_active
            );


        /*
        --------------------------------------------------
        Charger les catégories
        --------------------------------------------------
        */

        await loadCategories(
            product.category_id
        );


        /*
        --------------------------------------------------
        Afficher les images
        --------------------------------------------------
        */

        const images =
            product.images || [];


        if (images.length === 0) {

            currentImagesContainer.innerHTML = `

                <div class="col-12">

                    <div class="alert alert-light">
                        Aucune image pour ce produit.
                    </div>

                </div>

            `;

        } else {

            currentImagesContainer.innerHTML =
                images.map(
                    function(image, index) {

                        return `

                            <div class="col-6 col-md-4">

                                <div class="card border-0 shadow-sm">

                                    <img
                                        src="/storage/${image.image}"
                                        alt="${product.name}"
                                        class="card-img-top"
                                        style="
                                            height: 180px;
                                            object-fit: cover;
                                        "
                                    >

                                    <div class="card-body text-center">

                                        ${
                                            image.is_primary
                                            ?
                                            `
                                                <span class="badge text-bg-dark">
                                                    ⭐ Image principale
                                                </span>
                                            `
                                            :
                                            `
                                                <span class="badge text-bg-light">
                                                    Image ${index + 1}
                                                </span>

                                                <br>

                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-warning mt-2"
                                                    onclick="setPrimaryImage(${image.id})">

                                                    ⭐ Définir comme principale

                                                </button>
                                            `
                                        }

                                        <br>

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger mt-2"
                                            onclick="deleteProductImage(${image.id})">

                                            🗑️ Supprimer

                                        </button>

                                    </div>

                                </div>

                            </div>

                        `;

                    }
                ).join('');

        }


        /*
        --------------------------------------------------
        Afficher le formulaire
        --------------------------------------------------
        */

        loading.classList.add(
            'd-none'
        );

        editContainer.classList.remove(
            'd-none'
        );


    } catch (error) {

        console.error(
            'Erreur chargement produit :',
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
==========================================================
AJOUT DE NOUVELLES IMAGES
==========================================================
*/

async function uploadNewImages() {

    const files =
        Array.from(
            newImagesInput.files
        );


    if (files.length === 0) {

        return;

    }


    try {

        const responseProduct =
            await fetch(
                `http://127.0.0.1:8000/api/seller/products/${productId}`,
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


        const productData =
            await responseProduct.json();


        if (!responseProduct.ok) {

            throw new Error(
                productData.message ||
                'Impossible de vérifier les images.'
            );

        }


        const currentImages =
            productData.product.images || [];


        if (
            currentImages.length +
            files.length >
            8
        ) {

            throw new Error(
                `Vous ne pouvez pas dépasser 8 images. ` +
                `Le produit possède actuellement ` +
                `${currentImages.length} image(s).`
            );

        }


        const formData =
            new FormData();


        files.forEach(
            function(file) {

                formData.append(
                    'images[]',
                    file
                );

            }
        );


        const response =
            await fetch(
                `http://127.0.0.1:8000/api/seller/products/${productId}/images`,
                {
                    method: 'POST',

                    headers: {

                        'Accept':
                            'application/json',

                        'Authorization':
                            'Bearer ' + token

                    },

                    body: formData
                }
            );


        const data =
            await response.json();


        if (!response.ok) {

            throw new Error(
                data.message ||
                "Impossible d'ajouter les images."
            );

        }


        newImagesInput.value = '';

        newImagesPreview.innerHTML = '';

        await loadProduct();


    } catch (error) {

        console.error(
            'Erreur ajout images :',
            error
        );

        showError(
            error.message
        );

        throw error;

    }

}


/*
==========================================================
ENREGISTRER LES MODIFICATIONS
==========================================================
*/

editProductForm.addEventListener(
    'submit',
    async function(event) {

        event.preventDefault();

        hideError();

        updateButton.disabled =
            true;

        updateButton.textContent =
            'Enregistrement...';


        try {

            const response =
                await fetch(
                    `http://127.0.0.1:8000/api/seller/products/${productId}`,
                    {
                        method: 'PUT',

                        headers: {

                            'Content-Type':
                                'application/json',

                            'Accept':
                                'application/json',

                            'Authorization':
                                'Bearer ' + token

                        },

                        body: JSON.stringify({

                            name:
                                document.getElementById(
                                    'name'
                                ).value.trim(),

                            description:
                                document.getElementById(
                                    'description'
                                ).value.trim() ||
                                null,

                            price:
                                Number(
                                    document.getElementById(
                                        'price'
                                    ).value
                                ),

                            stock:
                                Number(
                                    document.getElementById(
                                        'stock'
                                    ).value
                                ),

                            /*
                            ----------------------------------
                            CATÉGORIE
                            ----------------------------------
                            */

                            category_id:
                                Number(
                                    categorySelect.value
                                ),

                            is_active:
                                document.getElementById(
                                    'is_active'
                                ).checked

                        })
                    }
                );


            const data =
                await response.json();


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
                    'Impossible de modifier le produit.'
                );

            }


            console.log(
                'Produit modifié :',
                data.product
            );


            /*
            --------------------------------------------------
            Ajouter les nouvelles images
            --------------------------------------------------
            */

            await uploadNewImages();


            /*
            --------------------------------------------------
            Tout est terminé
            --------------------------------------------------
            */

            window.location.href =
                '/seller/products';


        } catch (error) {

            console.error(
                'Erreur modification :',
                error
            );

            showError(
                error.message
            );


        } finally {

            updateButton.disabled =
                false;

            updateButton.textContent =
                'Enregistrer les modifications';

        }

    }
);


/*
==========================================================
DÉMARRAGE
==========================================================
*/

loadProduct();

</script>

@endsection
