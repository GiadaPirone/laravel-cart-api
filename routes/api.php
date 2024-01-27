<?php

use App\Http\Controllers\CartItemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/cart',[CartItemController::class, 'createCart']);
Route::post('/cart/{cartId}/item/{itemId}', [CartItemController::class, 'addToCart']);
Route::delete('/cart/{cartId}/item/{itemId}', [CartItemController::class, 'deleteFromCart']);
Route::get('/cart', [CartItemController::class, 'getAllCarts']);