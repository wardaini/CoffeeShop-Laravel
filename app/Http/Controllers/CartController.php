<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart  = session()->get('cart', []);
        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        return view('cart.index', compact('cart', 'total'));
    }

    public function add(Request $request, Product $product)
    {
        $cart = session()->get('cart', []);
        $key  = $product->id;

        if (isset($cart[$key])) {
            $cart[$key]['quantity'] += $request->input('quantity', 1);
        } else {
            $cart[$key] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => $product->price,
                'image'    => $product->image_url,
                'quantity' => $request->input('quantity', 1),
            ];
        }

        session()->put('cart', $cart);

        return redirect()->back()->with('success', $product->name . ' ditambahkan ke keranjang!');
    }

    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $qty = (int) $request->input('quantity', 1);
            if ($qty <= 0) {
                unset($cart[$id]);
            } else {
                $cart[$id]['quantity'] = $qty;
            }
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Keranjang diperbarui.');
    }

    public function remove($id)
    {
        $cart = session()->get('cart', []);
        unset($cart[$id]);
        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Item dihapus dari keranjang.');
    }
}