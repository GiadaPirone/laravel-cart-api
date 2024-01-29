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
            return response()->json(['error' => 'Carrello non creato: il prodotto inserito non esiste.'], 404);
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

            if ($exception->getModel() === Cart::class) {
                return response()->json(['error' => 'Carrello non trovato.'], 404);
            } elseif ($exception->getModel() === Item::class) {
                return response()->json(['error' => 'Prodotto non trovato.'], 404);
            }
        }
    }


    public function deleteFromCart($cartId, $itemId)
    {
        try {
            $cart = Cart::findOrFail($cartId);
    
            // Verifica se l'elemento esiste prima di tentare l'eliminazione
            $existingItem = $cart->items()->find($itemId);
    
            if ($existingItem) {
                // Elimina l'elemento solo se esiste nel carrello
                $cart->items()->detach($itemId);
    
                // Registra un messaggio nel file di log dedicato alle modifiche del carrello
                Log::channel('cart_modification_log')->info('Prodotto con ID ' . $itemId . ' rimosso dal carrello:' . $cartId . json_encode($cart));
    
                return response()->json([], 204);
            } else {
                return response()->json(['error' => 'Prodotto non trovato nel carrello.'], 404);
            }
    
        } catch (ModelNotFoundException $cartException) {
            return response()->json(['error' => 'Stai provando ad eliminare un prodotto in un carrello non esistente.'], 404);
    
        } catch (ModelNotFoundException $itemException) {
            return response()->json(['error' => 'Prodotto non trovato.'], 404);
        }
    }


    public function getAllCarts()
    {
        $carts = Cart::with('items')->get();

        return response()->json([
            'data' => $carts,
        ], 200);
    }
}
