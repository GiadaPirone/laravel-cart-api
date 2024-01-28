<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Item;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartItemController extends Controller
{
    public function createCart(Request $request)
    {
        try {
            // Cerca l'item nel database
            $item = Item::findOrFail($request->input('itemId'));

            // Se l'item esiste, crea il carrello
            $cart = Cart::create();
            $cart->items()->attach($item);

            $updatedCart = Cart::with('items')->findOrFail($cart->id);

            // Registra un messaggio nel file di log
            Log::channel('cart_log')->info('Carrello creato: ' . json_encode($cart));

            return response()->json([
                'data' => $updatedCart,
            ], 201);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Cart non creato: item inserito non esiste.'], 404);
        }
    }

    public function addToCart($cartId, $itemId)
    {
        try {

            // cerca cart/item nel db 
            $cart = Cart::findOrFail($cartId);
            $item = Item::findOrFail($itemId);

            // collega item al cart
            $cart->items()->attach($item);

            $updatedCart = Cart::with('items')->findOrFail($cart->id);

            // Registra un messaggio nel file di log dedicato alle modifiche del carrello
            Log::channel('cart_modification_log')->info('Prodotto con ID ' . $itemId . ' aggiunto al carrello:' . $cartId . json_encode($updatedCart));

            return response()->json([
                'data' => $updatedCart,
            ], 200);


        } catch (ModelNotFoundException $exception) {
            // Verifica il tipo di eccezione
            if ($exception->getModel() === Cart::class) {
                return response()->json(['error' => 'Carrello non trovato.'], 404);
                
            } elseif ($exception->getModel() === Item::class) {
                return response()->json(['error' => 'Elemento non trovato.'], 404);
            }
        }
    }

    public function deleteFromCart($cartId, $itemId)
    {
        $cart = Cart::findOrFail($cartId);

        $cart->items()->updateExistingPivot($itemId, ['deleted_at' => now()]);

        // Registra un messaggio nel file di log dedicato alle modifiche del carrello
        Log::channel('cart_modification_log')->info('Prodotto con ID ' . $itemId . ' rimosso dal carrello:' . $cartId . json_encode($cart));

        return response()->json([], 204);
    }

    public function getAllCarts()
    {
        $carts = Cart::with('items')->get();

        return response()->json([
            'data' => $carts,
        ], 200);
    }
}
