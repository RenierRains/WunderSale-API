<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Model\Item;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CartController extends Controller
{
    public function index(){
        $carts = Cart::where('user_id', Auth::id())->get();
        return view('cart.index', compact('carts'));
    }

    public function add(Request $request){
        $validated = $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric',
        ]);

        $totalPrice = $validated['price'] * $validated['quantity'];

        $cartItem = Cart::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'item_id' => $validated['item_id'],
            ],
            [
                'quantity' => $validated['quantity'],
                'total_price' => $totalPrice,
            ]
        );

        return response()->json(['message' => 'Item added to cart successfully!']);
    }

    public function remove(Request $request){
        $request->validate(['cart_id' => 'required|exists:carts,id']);

        $cartId = $request->cart_id;
        $cart = Cart::where('id', $cartId)->where('user_id', Auth::id())->first();

        if ($cart) {
            $cart->delete();
            return response()->json(['message' => 'Item removed successfully.']);
        }

        return response()->json(['message' => 'Item not found or you do not have permission to remove this item.'], 404);
    }

    public function changeQuantity(Request $request) {
        $cart = Cart::find($request->cart_id);
        if ($request->action == 'increment') {
            $cart->quantity++;
        } elseif ($request->action == 'decrement') {
            $cart->quantity > 1 ? $cart->quantity-- : 1;
        }
        $cart->save();
    
        $totalPrice = $cart->quantity * $cart->item->price;
    
        return response()->json([
            'success' => true,
            'quantity' => $cart->quantity,
            'total_price' => $totalPrice,
        ]);
    }



}
