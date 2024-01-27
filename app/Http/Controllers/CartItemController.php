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

   
}
