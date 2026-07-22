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
            'customer_email'   => 'nullable|email',
            'customer_phone'   => 'nullable|string|max:20',
            'notes'            => 'nullable|string|max:500',
            'items'            => 'required|array',
            'items.*.cart_key' => 'required',
            'items.*.item_order_type' => 'required|in:dine_in,take_away',
            'table_number'     => 'nullable|string|max:10',
            'delivery_address' => 'nullable|string|max:255',
            'payment_method'   => 'required|in:qris,dana,ovo,bsi,bank_aceh,cash',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong!');
        }

        // Tentukan order_type utama & take_away_method
        $itemTypes = collect($request->items)->pluck('item_order_type');
        $hasDineIn   = $itemTypes->contains('dine_in');
        $hasTakeAway = $itemTypes->contains('take_away');

        // Cek apakah ada take away delivery
        $hasDelivery = collect($request->items)
            ->filter(fn($i) => ($i['item_order_type'] ?? '') === 'take_away' && ($i['take_away_method'] ?? '') === 'delivery')
            ->isNotEmpty();

        // Order type: kalau ada keduanya → 'mixed', kalau hanya satu
        $orderType = $hasDineIn && $hasTakeAway ? 'mixed' : ($hasDineIn ? 'dine_in' : 'take_away');

        $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $deliveryFee = $hasDelivery ? 8000 : 0;

        $order = DB::transaction(function () use ($request, $cart, $total, $deliveryFee, $orderType, $hasDelivery) {
            $order = Order::create([
                'user_id'          => auth()->id(),
                'customer_name'    => $request->customer_name,
                'customer_email'   => $request->customer_email ?? 'guest@coffeeshop.local',
                'customer_phone'   => $request->customer_phone,
                'notes'            => $request->notes,
                'total_price'      => $total,
                'order_type'       => $orderType,
                'take_away_method' => $hasDelivery ? 'delivery' : ($orderType !== 'dine_in' ? 'pickup' : null),
                'table_number'     => $request->table_number,
                'delivery_address' => $request->delivery_address,
                'payment_method'   => $request->payment_method,
                'payment_status'   => 'unpaid',
                'delivery_fee'     => $deliveryFee,
            ]);

            // Buat order items dengan tipe per item
            foreach ($request->items as $itemData) {
                $cartKey  = $itemData['cart_key'];
                $cartItem = $cart[$cartKey] ?? null;
                if (!$cartItem) continue;

                $order->items()->create([
                    'product_id'      => $cartItem['id'],
                    'quantity'        => $cartItem['quantity'],
                    'price'           => $cartItem['price'],
                    'item_order_type' => $itemData['item_order_type'],
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