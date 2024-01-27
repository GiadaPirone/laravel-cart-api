<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Item;
use Illuminate\Http\Request;

class CartItemController extends Controller
{
    public function createCart(Request $request)
    {

        $cart = Cart::create();
        $item = Item::findOrFail($request->input('itemId'));

        $cart->items()->attach($item);
        $updatedCart = Cart::with('items')->findOrFail($cart->id);

        return response()->json([

            'data' => $updatedCart,

        ], 201);
    }

    public function addToCart($cartId, $itemId)
    {
        $cart = Cart::findOrFail($cartId);
        $item = Item::findOrFail($itemId);

        $cart->items()->attach($item);

        $updatedCart = Cart::with('items')->findOrFail($cart->id);

        return response()->json([
            'data' => $updatedCart,
        ], 200);
    }

    public function deleteFromCart($cartId, $itemId)
    {
        $cart = Cart::findOrFail($cartId);

        $cart->items()->updateExistingPivot($itemId, ['deleted_at' => now()]);

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
