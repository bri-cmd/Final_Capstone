<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CatalogueController extends Controller
{
    public function index(Request $request)
    {   
        // Start with a base query
        $query = Product::query();

        // Search filter (includes "motherboard" in search)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('name', 'like', '%motherboard%'); // Always include "motherboard"
            });
        }

        // Get distinct categories & brands for sidebar
        $categories = Product::select('category')->distinct()->pluck('category');
        $brands = Product::select('brand')->distinct()->pluck('brand');

        // 🔎 Filter by category
        if ($request->has('category') && $request->category) {
            if ($request->category === 'components') {
                // Show all components (no filter, show all products)
                // Optionally, you can filter by a list of component categories if needed
            } else {
                $query->where('category', $request->category);
            }
        }

        // 🔎 Filter by brand
        if ($request->has('brand') && $request->brand) {
            $query->where('brand', $request->brand);
        }

        // 🔎 Filter by price
        if ($request->has('min_price') && $request->min_price !== null) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price !== null) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort products
        switch ($request->sort) {
            case 'newest':
                $query->orderByDesc('created_at');
                break;
            case 'price_asc':
                $query->orderBy('price');
                break;
            case 'price_desc':
                $query->orderByDesc('price');
                break;
            case 'name_asc':
                $query->orderBy('name');
                break;
            case 'name_desc':
                $query->orderByDesc('name');
                break;
        }
        
        // Execute with pagination
        $products = $query->paginate(9);

        return view('catalogue', compact('products', 'categories', 'brands'));
    }
}



