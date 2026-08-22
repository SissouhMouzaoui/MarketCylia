<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * ==========================================================
     * AFFICHER LE PANIER
     * ==========================================================
     */
    public function index(Request $request)
    {
        $cartItems = $request->user()
            ->cartItems()
            ->with([
                'product.images' => function ($query) {

                    $query->orderBy('sort_order');

                },

                'product.category',

                'product.user',
            ])
            ->latest()
            ->get();


        return response()->json([
            'cart_items' => $cartItems,
        ]);
    }


    /**
     * ==========================================================
     * AJOUTER UN PRODUIT AU PANIER
     * ==========================================================
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
            ],

            'quantity' => [
                'sometimes',
                'integer',
                'min:1',
            ],

        ]);


        $product = Product::find(
            $validated['product_id']
        );


        /*
        ==========================================================
        PRODUIT ACTIF
        ==========================================================
        */

        if (!$product->is_active) {

            return response()->json([
                'message' =>
                    'Ce produit n’est plus disponible.',
            ], 422);
        }


        /*
        ==========================================================
        STOCK
        ==========================================================
        */

        if ($product->stock <= 0) {

            return response()->json([
                'message' =>
                    'Ce produit est en rupture de stock.',
            ], 422);
        }


        $quantity =
            $validated['quantity'] ?? 1;


        /*
        ==========================================================
        ARTICLE EXISTANT
        ==========================================================
        */

        $cartItem =
            $request->user()
                ->cartItems()
                ->where(
                    'product_id',
                    $product->id
                )
                ->first();


        $newQuantity =
            $cartItem
                ? $cartItem->quantity + $quantity
                : $quantity;


        /*
        ==========================================================
        VÉRIFIER LE STOCK
        ==========================================================
        */

        if ($newQuantity > $product->stock) {

            return response()->json([
                'message' =>
                    "Stock insuffisant. Il reste seulement " .
                    "{$product->stock} article(s).",
            ], 422);
        }


        /*
        ==========================================================
        CRÉER / METTRE À JOUR
        ==========================================================
        */

        if ($cartItem) {

            $cartItem->update([
                'quantity' => $newQuantity,
            ]);

        } else {

            $cartItem =
                $request->user()
                    ->cartItems()
                    ->create([

                        'product_id' =>
                            $product->id,

                        'quantity' =>
                            $quantity,

                    ]);
        }


        $cartItem->load([
            'product.images',
            'product.category',
            'product.user',
        ]);


        return response()->json([

            'message' =>
                'Produit ajouté au panier avec succès.',

            'cart_item' =>
                $cartItem,

        ], 201);
    }


    /**
     * ==========================================================
     * MODIFIER LA QUANTITÉ
     * ==========================================================
     */
    public function update(
        Request $request,
        CartItem $cartItem
    ) {
        /*
        ==========================================================
        VÉRIFIER LE PROPRIÉTAIRE
        ==========================================================
        */

        if (
            $cartItem->user_id !==
            $request->user()->id
        ) {

            return response()->json([
                'message' =>
                    'Accès refusé.',
            ], 403);
        }


        /*
        ==========================================================
        VALIDATION
        ==========================================================
        */

        $validated =
            $request->validate([

                'quantity' => [
                    'required',
                    'integer',
                    'min:1',
                ],

            ]);


        /*
        ==========================================================
        PRODUIT
        ==========================================================
        */

        $product =
            $cartItem->product;


        /*
        ==========================================================
        VÉRIFIER LE STOCK
        ==========================================================
        */

        if (
            $validated['quantity'] >
            $product->stock
        ) {

            return response()->json([
                'message' =>
                    "Stock insuffisant. Il reste seulement " .
                    "{$product->stock} article(s).",
            ], 422);
        }


        /*
        ==========================================================
        MODIFIER
        ==========================================================
        */

        $cartItem->update([

            'quantity' =>
                $validated['quantity'],

        ]);


        /*
        ==========================================================
        RECHARGER
        ==========================================================
        */

        $cartItem->load([
            'product.images',
            'product.category',
            'product.user',
        ]);


        return response()->json([

            'message' =>
                'Quantité mise à jour.',

            'cart_item' =>
                $cartItem,

        ]);
    }


    /**
     * ==========================================================
     * SUPPRIMER UN ARTICLE
     * ==========================================================
     */
    public function destroy(
        Request $request,
        CartItem $cartItem
    ) {
        /*
        ==========================================================
        VÉRIFIER LE PROPRIÉTAIRE
        ==========================================================
        */

        if (
            $cartItem->user_id !==
            $request->user()->id
        ) {

            return response()->json([
                'message' =>
                    'Accès refusé.',
            ], 403);
        }


        /*
        ==========================================================
        SUPPRESSION
        ==========================================================
        */

        $cartItem->delete();


        return response()->json([

            'message' =>
                'Produit supprimé du panier.',

        ]);
    }
}
