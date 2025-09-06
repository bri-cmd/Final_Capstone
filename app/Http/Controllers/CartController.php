<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    // Show cart page
    public function index()
    {
        $cart = session()->get('cart', []);
        return view('cart', compact('cart'));
    }

    // Add product to cart
    public function add(Request $request)
{
    $cart = session()->get('cart', []);

    // Try to find product by ID
    $product = Product::find($request->product_id);

    if ($product) {
        $id = $product->id;
        $name = $product->name;
        $price = $product->price;
    } else {
        // Fallback (for products that caused 404)
        $id = uniqid('custom_'); // generate unique key to avoid overlap
        $name = $request->name;
        $price = $request->price;
    }

    if (isset($cart[$id])) {
        $cart[$id]['quantity']++;
    } else {
        $cart[$id] = [
            "name" => $name,
            "price" => $price,
            "quantity" => 1,
        ];
    }

    session()->put('cart', $cart);

    return redirect()->back()->with('success', $name . ' added to cart!');
}



    // Update quantity
    public function update(Request $request, $id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            if ($request->action === 'increase') {
                $cart[$id]['quantity']++;
            } elseif ($request->action === 'decrease' && $cart[$id]['quantity'] > 1) {
                $cart[$id]['quantity']--;
            }
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Cart updated successfully!');
    }

    // Remove product
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Product removed from cart!');
    }

    // Checkout selected items
    public function checkout(Request $request)
    {
        $selectedItems = json_decode($request->get('selected_items'), true);

        if (empty($selectedItems)) {
            return redirect()->route('cart.index')->with('error', 'No items selected for checkout.');
        }

        $cart = session()->get('cart', []);
        $items = array_intersect_key($cart, array_flip($selectedItems));

        return view('checkout', compact('items'));
    }
}
