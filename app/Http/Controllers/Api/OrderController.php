<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MES COMMANDES - CLIENT
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $orders = $request->user()
            ->orders()
            ->with([
                'items.product.images',
                'items.product.category',
                'items.product.user',
            ])
            ->latest()
            ->get();

        return response()->json([
            'orders' => $orders,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DÉTAIL D'UNE COMMANDE - CLIENT
    |--------------------------------------------------------------------------
    */

    public function show(
        Request $request,
        Order $order
    ) {
        if ($order->user_id !== $request->user()->id) {

            return response()->json([
                'message' => 'Accès refusé.',
            ], 403);
        }

        $order->load([
            'items.product.images',
            'items.product.category',
            'items.product.user',
        ]);

        return response()->json([
            'order' => $order,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | CRÉER LES COMMANDES À PARTIR DU PANIER
    |--------------------------------------------------------------------------
    |
    | IMPORTANT :
    |
    | Si le panier contient des produits de plusieurs vendeurs,
    | nous créons une commande indépendante pour chaque vendeur.
    |
    */

    public function store(Request $request)
    {
        $user = $request->user();

        $orders = DB::transaction(function () use ($user) {

            /*
            |--------------------------------------------------------------------------
            | Récupérer le panier
            |--------------------------------------------------------------------------
            */

            $cartItems = $user->cartItems()
                ->with([
                    'product',
                    'product.user',
                ])
                ->lockForUpdate()
                ->get();


            /*
            |--------------------------------------------------------------------------
            | Panier vide
            |--------------------------------------------------------------------------
            */

            if ($cartItems->isEmpty()) {

                abort(
                    422,
                    'Votre panier est vide.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Vérifier tous les produits avant de créer
            | les commandes
            |--------------------------------------------------------------------------
            */

            foreach ($cartItems as $cartItem) {

                $product = $cartItem->product;


                /*
                |------------------------------------------------------------------
                | Produit supprimé
                |------------------------------------------------------------------
                */

                if (!$product) {

                    abort(
                        422,
                        'Un produit de votre panier n\'existe plus.'
                    );
                }


                /*
                |------------------------------------------------------------------
                | Produit inactif
                |------------------------------------------------------------------
                */

                if (!$product->is_active) {

                    abort(
                        422,
                        "Le produit \"{$product->name}\" n'est plus disponible."
                    );
                }


                /*
                |------------------------------------------------------------------
                | Vérifier le vendeur
                |------------------------------------------------------------------
                */

                if (!$product->user_id) {

                    abort(
                        422,
                        "Le produit \"{$product->name}\" n'a pas de vendeur."
                    );
                }


                /*
                |------------------------------------------------------------------
                | Vérifier le stock
                |------------------------------------------------------------------
                */

                if (
                    $cartItem->quantity >
                    $product->stock
                ) {

                    abort(
                        422,
                        "Stock insuffisant pour le produit \"{$product->name}\"."
                    );
                }
            }


            /*
            |--------------------------------------------------------------------------
            | REGROUPER LES ARTICLES PAR VENDEUR
            |--------------------------------------------------------------------------
            */

            $itemsBySeller =
                $cartItems->groupBy(
                    function ($cartItem) {

                        return $cartItem->product->user_id;
                    }
                );


            /*
            |--------------------------------------------------------------------------
            | Créer une commande pour chaque vendeur
            |--------------------------------------------------------------------------
            */

            $orders = collect();


            foreach (
                $itemsBySeller
                as $sellerId => $sellerItems
            ) {

                /*
                |------------------------------------------------------------------
                | Total de cette commande
                |------------------------------------------------------------------
                */

                $total = 0;


                foreach ($sellerItems as $cartItem) {

                    $product =
                        $cartItem->product;


                    $total +=
                        $product->price *
                        $cartItem->quantity;
                }


                /*
                |------------------------------------------------------------------
                | Créer la commande du vendeur
                |------------------------------------------------------------------
                */

                $order =
                    $user->orders()->create([

                        'order_number' =>
                            'MC-' .
                            now()->format('YmdHis') .
                            '-' .
                            strtoupper(
                                Str::random(5)
                            ),

                        'status' =>
                            'pending',

                        'total' =>
                            $total,
                    ]);


                /*
                |------------------------------------------------------------------
                | Créer les lignes de cette commande
                |------------------------------------------------------------------
                */

                foreach ($sellerItems as $cartItem) {

                    $product =
                        $cartItem->product;


                    $price =
                        $product->price;


                    $quantity =
                        $cartItem->quantity;


                    $subtotal =
                        $price *
                        $quantity;


                    $order->items()->create([

                        'product_id' =>
                            $product->id,

                        'product_name' =>
                            $product->name,

                        'price' =>
                            $price,

                        'quantity' =>
                            $quantity,

                        'subtotal' =>
                            $subtotal,
                    ]);


                    /*
                    |------------------------------------------------------------------
                    | Diminuer le stock
                    |------------------------------------------------------------------
                    */

                    $product->decrement(
                        'stock',
                        $quantity
                    );
                }


                /*
                |------------------------------------------------------------------
                | Charger les relations
                |------------------------------------------------------------------
                */

                $order->load([
                    'items.product.images',
                    'items.product.category',
                    'items.product.user',
                ]);


                /*
                |------------------------------------------------------------------
                | Ajouter la commande à la collection
                |------------------------------------------------------------------
                */

                $orders->push($order);
            }


            /*
            |--------------------------------------------------------------------------
            | Vider le panier APRÈS avoir créé toutes les commandes
            |--------------------------------------------------------------------------
            */

            $user->cartItems()->delete();


            return $orders;
        });


        /*
        |--------------------------------------------------------------------------
        | Réponse
        |--------------------------------------------------------------------------
        |
        | "orders" contient toutes les commandes créées.
        |
        | "order" est conservé pour compatibilité avec l'ancien frontend
        | lorsque le panier ne contient qu'un seul vendeur.
        |
        */

        return response()->json([

            'message' =>
                'Commande(s) créée(s) avec succès.',

            'orders' =>
                $orders,

            'order' =>
                $orders->first(),

        ], 201);
    }


    /*
    |--------------------------------------------------------------------------
    | ANNULER UNE COMMANDE - CLIENT
    |--------------------------------------------------------------------------
    */

    public function cancel(
        Request $request,
        Order $order
    ) {
        if (
            $order->user_id !==
            $request->user()->id
        ) {

            return response()->json([
                'message' => 'Accès refusé.',
            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | Déjà annulée
        |--------------------------------------------------------------------------
        */

        if ($order->status === 'cancelled') {

            return response()->json([

                'message' =>
                    'Cette commande est déjà annulée.',

                'order' =>
                    $order->fresh(),

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Seules les commandes en attente peuvent être annulées
        |--------------------------------------------------------------------------
        */

        if ($order->status !== 'pending') {

            return response()->json([

                'message' =>
                    'Cette commande ne peut plus être annulée.',

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Annuler + restituer le stock
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use ($order) {

            $order->load('items.product');


            foreach ($order->items as $item) {

                if ($item->product) {

                    $item->product->increment(
                        'stock',
                        $item->quantity
                    );
                }
            }


            $order->update([
                'status' => 'cancelled',
            ]);
        });


        return response()->json([

            'message' =>
                'Commande annulée avec succès. Le stock a été restauré.',

            'order' =>
                $order->fresh(),

        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | COMMANDES DU VENDEUR
    |--------------------------------------------------------------------------
    */

    public function sellerIndex(Request $request)
    {
        $sellerId =
            $request->user()->id;


        /*
        |--------------------------------------------------------------------------
        | Le vendeur ne reçoit que les commandes
        | contenant ses propres produits
        |--------------------------------------------------------------------------
        */

        $orders = Order::query()

            ->whereHas(
                'items.product',
                function ($query) use ($sellerId) {

                    $query->where(
                        'user_id',
                        $sellerId
                    );
                }
            )

            ->with([
                'user',
                'items.product',
                'items.product.images',
                'items.product.category',
            ])

            ->latest()

            ->get();


        return response()->json([
            'orders' => $orders,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | DÉTAIL D'UNE COMMANDE - VENDEUR
    |--------------------------------------------------------------------------
    */

    public function sellerShow(
        Request $request,
        Order $order
    ) {
        $sellerId =
            $request->user()->id;


        /*
        |--------------------------------------------------------------------------
        | Vérifier que cette commande appartient au vendeur
        |--------------------------------------------------------------------------
        */

        $belongsToSeller =
            $order->items()
                ->whereHas(
                    'product',
                    function ($query) use ($sellerId) {

                        $query->where(
                            'user_id',
                            $sellerId
                        );
                    }
                )
                ->exists();


        if (!$belongsToSeller) {

            return response()->json([
                'message' => 'Accès refusé.',
            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | Charger la commande
        |--------------------------------------------------------------------------
        */

        $order->load([
            'user',
            'items.product',
            'items.product.images',
            'items.product.category',
        ]);


        return response()->json([
            'order' => $order,
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | MODIFIER LE STATUT PAR LE VENDEUR
    |--------------------------------------------------------------------------
    */

    public function sellerUpdateStatus(
        Request $request,
        Order $order
    ) {
        $sellerId =
            $request->user()->id;


        /*
        |--------------------------------------------------------------------------
        | Vérifier que la commande contient
        | un produit appartenant au vendeur
        |--------------------------------------------------------------------------
        */

        $belongsToSeller =
            $order->items()
                ->whereHas(
                    'product',
                    function ($query) use ($sellerId) {

                        $query->where(
                            'user_id',
                            $sellerId
                        );
                    }
                )
                ->exists();


        if (!$belongsToSeller) {

            return response()->json([
                'message' => 'Accès refusé.',
            ], 403);
        }


        /*
        |--------------------------------------------------------------------------
        | Validation
        |--------------------------------------------------------------------------
        */

        $validated =
            $request->validate([

                'status' => [
                    'required',
                    'in:pending,confirmed,shipped,delivered,cancelled',
                ],

            ]);


        $newStatus =
            $validated['status'];


        $oldStatus =
            $order->status;


        /*
        |--------------------------------------------------------------------------
        | Commande déjà annulée
        |--------------------------------------------------------------------------
        */

        if (
            $oldStatus === 'cancelled' &&
            $newStatus !== 'cancelled'
        ) {

            return response()->json([

                'message' =>
                    'Une commande annulée ne peut plus être réactivée.',

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | Commande déjà livrée
        |--------------------------------------------------------------------------
        */

        if (
            $oldStatus === 'delivered' &&
            $newStatus !== 'delivered'
        ) {

            return response()->json([

                'message' =>
                    'Une commande livrée ne peut plus être modifiée.',

            ], 422);
        }


        /*
        |--------------------------------------------------------------------------
        | ANNULATION PAR LE VENDEUR
        |--------------------------------------------------------------------------
        */

        if (
            $newStatus === 'cancelled' &&
            $oldStatus !== 'cancelled'
        ) {

            DB::transaction(function () use ($order) {

                $order->load('items.product');


                /*
                |------------------------------------------------------------------
                | Restaurer le stock
                |------------------------------------------------------------------
                */

                foreach ($order->items as $item) {

                    if ($item->product) {

                        $item->product->increment(
                            'stock',
                            $item->quantity
                        );
                    }
                }


                /*
                |------------------------------------------------------------------
                | Annuler la commande
                |------------------------------------------------------------------
                */

                $order->update([
                    'status' => 'cancelled',
                ]);
            });


            return response()->json([

                'message' =>
                    'Commande annulée avec succès. Le stock a été restauré.',

                'order' =>
                    $order->fresh(),

            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Modifier normalement le statut
        |--------------------------------------------------------------------------
        */

        $order->update([
            'status' =>
                $newStatus,
        ]);


        return response()->json([

            'message' =>
                'Statut de la commande mis à jour.',

            'order' =>
                $order->fresh(),

        ]);
    }
}
