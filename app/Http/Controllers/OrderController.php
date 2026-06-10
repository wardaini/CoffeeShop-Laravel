<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function checkout()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong!');
        }

        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        return view('order.checkout', compact('cart', 'total'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_name'  => 'required|string|max:100',
            'customer_email' => 'required|email',
            'customer_phone' => 'nullable|string|max:20',
            'notes'          => 'nullable|string|max:500',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong!');
        }

        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        DB::transaction(function () use ($request, $cart, $total) {
            $order = Order::create([
                'customer_name'  => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'notes'          => $request->notes,
                'total_price'    => $total,
            ]);

            foreach ($cart as $item) {
                $order->items()->create([
                    'product_id' => $item['id'],
                    'quantity'   => $item['quantity'],
                    'price'      => $item['price'],
                ]);
            }

            session()->forget('cart');
            session()->put('last_order_id', $order->id);
        });

        return redirect()->route('order.success');
    }

    public function success()
    {
        $orderId = session()->get('last_order_id');
        $order   = $orderId ? Order::with('items.product')->find($orderId) : null;

        return view('order.success', compact('order'));
    }

    public function track(Request $request)
    {
        $order = null;

        if ($request->filled('order_code')) {
            $order = Order::with('items.product')
                ->where('order_code', strtoupper($request->order_code))
                ->first();
        }

        return view('order.track', compact('order'));
    }
}