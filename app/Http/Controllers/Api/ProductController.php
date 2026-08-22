<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Category;

class ProductController extends Controller
{

/**
 * ==========================================================
 * AFFICHER UN PRODUIT ACTIF PUBLIQUEMENT
 * ==========================================================
 */
public function publicShow(Product $product)
{
    if (!$product->is_active) {

        return response()->json([
            'message' => 'Produit indisponible.'
        ], 404);
    }

    $product->load([
        'images' => function ($query) {
            $query->orderBy('sort_order');
        },
        'category',
        'user',
    ]);

    return response()->json([
        'product' => $product
    ]);
}


/**
 * ==========================================================
 * AFFICHER LES PRODUITS ACTIFS PUBLIQUEMENT
 * ==========================================================
 */
public function publicIndex()
{
    $products = Product::query()
        ->where('is_active', true)
        ->with([
            'images' => function ($query) {
                $query->orderBy('sort_order');
            },
            'category',
            'user',
        ])
        ->latest()
        ->get();

    return response()->json([
        'products' => $products,
    ]);
}

  /**
 * Affiche les produits du vendeur connecté.
 */
public function index(Request $request)
{
    // Récupération des produits appartenant au vendeur connecté
    // avec leurs images.
    $products = $request->user()
        ->products()
        ->with('images')
        ->latest()
        ->get();

    return response()->json([
        'products' => $products,
    ]);
}


    /**
     * Crée un nouveau produit pour le vendeur connecté.
     */
    /**
 * Crée un nouveau produit pour le vendeur connecté.
 */
public function store(Request $request)
{
    // Validation des informations du produit
    // et des images envoyées.
    $validated = $request->validate([

        'name' => [
            'required',
            'string',
            'max:255'
        ],

        'description' => [
            'nullable',
            'string'
        ],

        'price' => [
            'required',
            'numeric',
            'min:0'
        ],

        'stock' => [
            'required',
            'integer',
            'min:0'
        ],

        /*
        |--------------------------------------------------------------------------
        | Catégorie
        |--------------------------------------------------------------------------
        */

        'category_id' => [
            'required',
            'integer',
            'exists:categories,id'
        ],

        'image' => [
            'nullable',
            'string',
            'max:255'
        ],

        'is_active' => [
            'sometimes',
            'boolean'
        ],

        /*
        |--------------------------------------------------------------------------
        | Images
        |--------------------------------------------------------------------------
        */

        'images' => [
            'nullable',
            'array',
            'max:8'
        ],

        'images.*' => [
            'file',
            'image',
            'mimes:jpeg,jpg,png,webp',
            'max:5120',
        ],
    ]);


    /*
    ==========================================================
    RÉCUPÉRATION DES IMAGES
    ==========================================================
    */

    $images = $request->file('images', []);

    unset($validated['images']);


    /*
    ==========================================================
    ASSOCIATION AVEC LE VENDEUR CONNECTÉ
    ==========================================================
    */

    $validated['user_id'] =
        $request->user()->id;


    /*
    ==========================================================
    TRANSACTION
    ==========================================================
    */

    $product = DB::transaction(function () use (
        $validated,
        $images
    ) {

        /*
        ------------------------------------------------------
        Création du produit
        ------------------------------------------------------
        */

        $product = Product::create(
            $validated
        );


        /*
        ------------------------------------------------------
        Enregistrement des images
        ------------------------------------------------------
        */

        foreach ($images as $index => $image) {

            $path = $image->store(
                'products',
                'public'
            );


            $product->images()->create([

                'image' => $path,

                // Première image = image principale
                'is_primary' => $index === 0,

                // Ordre d'affichage
                'sort_order' => $index,

            ]);
        }


        return $product;
    });


    /*
    ==========================================================
    CHARGER LES RELATIONS
    ==========================================================
    */

    $product->load([
        'images',
        'category',
        'user'
    ]);


    /*
    ==========================================================
    RÉPONSE
    ==========================================================
    */

    return response()->json([

        'message' =>
            'Produit créé avec succès.',

        'product' =>
            $product,

    ], 201);
}


    /**
 * Affiche un produit appartenant au vendeur connecté.
 */
public function show(Request $request, Product $product)
{/**

* Afficher les détails d'un produit actif publiquement.
  */

  # VÉRIFIER SI LE PRODUIT EST ACTIF


  if (!$product->is_active) {
   return response()->json([
       'message' => 'Produit indisponible.'
   ], 404);


  }

  # /*

  # CHARGER LES RELATIONS


  $product->load([
  'images' => function ($query) {


       $query->orderBy('sort_order');

   },

   'category',

   'user'

  ]);

  # /*

  # RÉPONSE


  return response()->json([
  'product' => $product
  ]);
  }



    /**
     * Met à jour un produit appartenant au vendeur connecté.
     */
    public function update(Request $request, Product $product)
    {
        // Vérification du propriétaire.
        if ($product->user_id !== $request->user()->id) {

            return response()->json([
                'message' => 'Accès refusé.',
            ], 403);
        }


        $validated = $request->validate([
    'name' => ['sometimes', 'string', 'max:255'],

    'description' => ['nullable', 'string'],

    'price' => ['sometimes', 'numeric', 'min:0'],

    'stock' => ['sometimes', 'integer', 'min:0'],

    /*
    |--------------------------------------------------------------------------
    | Catégorie du produit
    |--------------------------------------------------------------------------
    */

    'category_id' => [
        'sometimes',
        'integer',
        'exists:categories,id',
    ],

    'image' => ['nullable', 'string', 'max:255'],

    'is_active' => ['sometimes', 'boolean'],

    /*
    |--------------------------------------------------------------------------
    | Nouvelles images
    |--------------------------------------------------------------------------
    */

    'images' => [
        'nullable',
        'array',
        'max:8',
    ],

    'images.*' => [
        'file',
        'image',
        'mimes:jpeg,jpg,png,webp',
        'max:10240',
    ],
]);


        // On retire images avant Product::update().
        unset($validated['images']);


        // Mise à jour des informations du produit.
        $product->update($validated);


        /*
        ==========================================================
        AJOUT DE NOUVELLES IMAGES
        ==========================================================
        */

        if ($request->hasFile('images')) {

            $images = $request->file('images');


            // Nombre actuel d'images.
            $currentImagesCount =
                $product->images()->count();


            foreach ($images as $index => $image) {

                // Vérification de la limite totale.
                if ($currentImagesCount >= 8) {
                    break;
                }


                $path = $image->store(
                    'products',
                    'public'
                );


                // La première image du produit devient principale
                // uniquement si le produit n'en possède aucune.
                $isPrimary =
                    $product->images()->count() === 0;


                $product->images()->create([
                    'image' => $path,
                    'is_primary' => $isPrimary,
                    'sort_order' =>
                        $product->images()->max('sort_order') + 1,
                ]);


                $currentImagesCount++;
            }
        }


        // Rechargement avec les images.category
        $product->load([
    'images',
    'category',
]);


        return response()->json([
    'message' => 'Produit mis à jour avec succès.',
    'product' => $product->fresh([
        'images',
        'category',
    ]),
]);
    }


    /**
     * Supprime un produit appartenant au vendeur connecté.
     */
    public function destroy(Request $request, Product $product)
    {
        // Vérification du propriétaire.
        if ($product->user_id !== $request->user()->id) {

            return response()->json([
                'message' => 'Accès refusé.',
            ], 403);
        }


        /*
        ==========================================================
        SUPPRESSION DES FICHIERS IMAGES
        ==========================================================
        */

        foreach ($product->images as $productImage) {

            if ($productImage->image) {

                Storage::disk('public')
                    ->delete($productImage->image);
            }
        }


        // Suppression du produit.
        // La relation cascade supprimera également
        // les enregistrements dans product_images.
        $product->delete();


        return response()->json([
            'message' => 'Produit supprimé avec succès.',
        ]);
    }
    /**
 * Supprime une image appartenant au produit du vendeur connecté.
 */
public function destroyImage(
    Request $request,
    Product $product,
    \App\Models\ProductImage $image
) {
    // Vérification du propriétaire du produit.
    if ($product->user_id !== $request->user()->id) {

        return response()->json([
            'message' => 'Accès refusé.',
        ], 403);
    }


    // Vérification que l'image appartient bien au produit.
    if ($image->product_id !== $product->id) {

        return response()->json([
            'message' => 'Cette image n\'appartient pas à ce produit.',
        ], 403);
    }


    // Empêcher la suppression de la dernière image.
    $imagesCount =
        $product->images()->count();

    if ($imagesCount <= 1) {

        return response()->json([
            'message' =>
                'Le produit doit conserver au moins une image.',
        ], 422);
    }


    // Suppression du fichier physique.
    if ($image->image) {

        \Illuminate\Support\Facades\Storage::disk('public')
            ->delete($image->image);
    }


    // Suppression de l'enregistrement.
    $image->delete();


    return response()->json([
        'message' => 'Image supprimée avec succès.',
    ]);
}
/**
 * Définit une image comme image principale du produit.
 */
public function setPrimaryImage(
    Request $request,
    Product $product,
    \App\Models\ProductImage $image
) {
    // Vérification du propriétaire du produit.
    if ($product->user_id !== $request->user()->id) {

        return response()->json([
            'message' => 'Accès refusé.',
        ], 403);
    }

    // Vérification que l'image appartient au produit.
    if ($image->product_id !== $product->id) {

        return response()->json([
            'message' =>
                'Cette image n\'appartient pas à ce produit.',
        ], 403);
    }

    // Retirer le statut principal de toutes les images.
    $product->images()->update([
        'is_primary' => false,
    ]);

    // Définir cette image comme principale.
    $image->update([
        'is_primary' => true,
    ]);

    return response()->json([
        'message' =>
            'Image principale mise à jour avec succès.',
        'image' => $image->fresh(),
    ]);
}
/**
 * Ajoute de nouvelles images à un produit.
 */
public function storeImages(
    Request $request,
    Product $product
) {
    // Vérification du propriétaire.
    if ($product->user_id !== $request->user()->id) {

        return response()->json([
            'message' => 'Accès refusé.',
        ], 403);
    }


    // Validation des images.
    $validated = $request->validate([
        'images' => ['required', 'array', 'max:8'],
        'images.*' => [
            'required',
            'image',
            'mimes:jpeg,jpg,png,webp',
            'max:5120',
        ],
    ]);


    // Nombre d'images actuelles.
    $currentCount =
        $product->images()->count();


    // Nombre de nouvelles images.
    $newCount =
        count($validated['images']);


    // Vérification de la limite totale.
    if (($currentCount + $newCount) > 8) {

        return response()->json([
            'message' =>
                "Le produit ne peut pas avoir plus de 8 images. " .
                "Il possède actuellement {$currentCount} image(s).",
        ], 422);
    }


    // Déterminer l'ordre de départ.
    $sortOrder =
        $product->images()->max('sort_order');

    $sortOrder =
        is_null($sortOrder)
        ? 0
        : $sortOrder + 1;


    /*
    --------------------------------------------------
    Enregistrer les nouvelles images
    --------------------------------------------------
    */

    foreach ($validated['images'] as $image) {

        $path =
            $image->store(
                'products',
                'public'
            );


        $product->images()->create([
            'image' => $path,
            'is_primary' => false,
            'sort_order' => $sortOrder,
        ]);


        $sortOrder++;
    }


    return response()->json([
        'message' =>
            'Images ajoutées avec succès.',

        'images' =>
            $product->images()
                ->orderBy('sort_order')
                ->get(),
    ]);
}


}
