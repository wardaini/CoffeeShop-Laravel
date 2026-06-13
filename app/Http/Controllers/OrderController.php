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
            'customer_name'    => 'required|string|max:100',
            'customer_email'   => 'required|email',
            'customer_phone'   => 'nullable|string|max:20',
            'notes'            => 'nullable|string|max:500',
            'order_type'       => 'required|in:dine_in,take_away',
            'table_number'     => 'required_if:order_type,dine_in|nullable|string|max:10',
            'take_away_method' => 'required_if:order_type,take_away|nullable|in:delivery,pickup',
            'delivery_address' => 'required_if:take_away_method,delivery|nullable|string|max:255',
            'payment_method'   => 'required|in:qris,dana,ovo,bsi,bank_aceh,cash',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong!');
        }

        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

        // Ongkir flat untuk delivery
        $deliveryFee = ($request->order_type === 'take_away' && $request->take_away_method === 'delivery')
            ? 8000
            : 0;

        $order = DB::transaction(function () use ($request, $cart, $total, $deliveryFee) {
            $order = Order::create([
                'user_id'          => auth()->id(),
                'customer_name'    => $request->customer_name,
                'customer_email'   => $request->customer_email,
                'customer_phone'   => $request->customer_phone,
                'notes'            => $request->notes,
                'total_price'      => $total,
                'order_type'       => $request->order_type,
                'take_away_method' => $request->order_type === 'take_away' ? $request->take_away_method : null,
                'table_number'     => $request->order_type === 'dine_in' ? $request->table_number : null,
                'delivery_address' => $request->take_away_method === 'delivery' ? $request->delivery_address : null,
                'payment_method'   => $request->payment_method,
                'payment_status'   => $request->payment_method === 'cash' ? 'unpaid' : 'unpaid',
                'delivery_fee'     => $deliveryFee,
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

            return $order;
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

    /**
     * Download struk PDF
     */
    public function receipt(Order $order)
    {
        $order->load('items.product');

        $pdf = \PDF::loadView('order.receipt-pdf', compact('order'));

        return $pdf->download('struk-' . $order->order_code . '.pdf');
    }
}