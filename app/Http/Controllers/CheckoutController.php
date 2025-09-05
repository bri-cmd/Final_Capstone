<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function process(Request $request)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->back()->with('error', 'Your cart is empty!');
        }

        $grandTotal = collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']);

        // 1️⃣ insert into orders
        $orderId = DB::table('orders')->insertGetId([
            'guest_name'     => $request->guest_name ?? 'Guest User',
            'guest_email'    => $request->guest_email ?? 'guest@example.com',
            'guest_phone'    => $request->guest_phone ?? '0000000000',
            'address'        => $request->address ?? 'Unknown',
            'payment_method' => $request->payment_method ?? 'Cash on Pickup',
            'total'          => $grandTotal,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        // 2️⃣ insert order items
        foreach ($cart as $productId => $item) {
            DB::table('order_items')->insert([
                'order_id'  => $orderId,
                'product_id'=> $productId,
                'name'      => $item['name'],
                'quantity'  => $item['quantity'],
                'price'     => $item['price'],
                'subtotal'  => $item['price'] * $item['quantity'],
                'created_at'=> now(),
                'updated_at'=> now(),
            ]);
        }

        // 3️⃣ clear cart
        session()->forget('cart');

        return redirect()->route('cart.index')->with('success', 'Order placed successfully!');
    }
}
