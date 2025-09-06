<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class CatalogueController extends Controller
{
    public function index(Request $request)
    {
        // 1) Pull from all component tables that exist
        $maps = [
            'cpus'         => 'CPU',
            'gpus'         => 'GPU',
            'motherboards' => 'Motherboard',
            'rams'         => 'RAM',
            'storages'     => 'Storage',
            'psus'         => 'PSU',
            'cases'        => 'Case',     // ok even if table doesn't exist; we skip it below
            'coolers'      => 'Cooler',
        ];

        $all = collect();

        foreach ($maps as $table => $category) {
            if (!Schema::hasTable($table)) {
                continue; // skip cleanly if teammate didn't migrate this table yet
            }

            // We only select columns that are common across tables.
            // If a column is missing in a specific table, DB will still return null for it.
            $rows = DB::table($table)->select([
                "{$table}.id",
                "{$table}.brand",
                "{$table}.model",
                "{$table}.price",
                "{$table}.stock",
                "{$table}.image",
                "{$table}.created_at",
            ])->get();

            // Normalize every row into a single shape for the Blade
            $normalized = $rows->map(function ($row) use ($category, $table) {
                $image = $row->image ? asset('storage/' . ltrim($row->image, '/')) : asset('images/placeholder.png');

                return [
                    'id'             => (int) ($row->id ?? 0),
                    'component_type' => strtolower($category),  // e.g., "cpu"
                    'table'          => $table,                 // e.g., "cpus"
                    'name'           => trim(($row->brand ?? '') . ' ' . ($row->model ?? '')),
                    'brand'          => (string) ($row->brand ?? ''),
                    'category'       => $category,              // e.g., "CPU"
                    'price'          => (float) ($row->price ?? 0),
                    'stock'          => (int) ($row->stock ?? 0),
                    'image'          => $image,
                    'created_at'     => $row->created_at ?? now(),
                    // You can add more normalized fields later if needed (rating, etc.)
                ];
            });

            $all = $all->merge($normalized);
        }

        // 2) Filters
        $filtered = $all;

        // Search
        if ($request->filled('search')) {
            $q = mb_strtolower($request->input('search'));
            $filtered = $filtered->filter(function ($item) use ($q) {
                $hay = mb_strtolower($item['name'] . ' ' . $item['brand'] . ' ' . $item['category']);
                return strpos($hay, $q) !== false;
            });
        }

        // Category
        if ($request->filled('category')) {
            $filtered = $filtered->filter(fn ($i) => $i['category'] === $request->category);
        }

        // Brand
        if ($request->filled('brand')) {
            $filtered = $filtered->filter(fn ($i) => $i['brand'] === $request->brand);
        }

        // Price range
        if ($request->filled('min_price')) {
            $min = (float) $request->min_price;
            $filtered = $filtered->filter(fn ($i) => $i['price'] >= $min);
        }
        if ($request->filled('max_price')) {
            $max = (float) $request->max_price;
            $filtered = $filtered->filter(fn ($i) => $i['price'] <= $max);
        }

        // 3) Sorting
        switch ($request->input('sort')) {
            case 'newest':
                $filtered = $filtered->sortByDesc('created_at');
                break;
            case 'price_asc':
                $filtered = $filtered->sortBy('price');
                break;
            case 'price_desc':
                $filtered = $filtered->sortByDesc('price');
                break;
            case 'name_asc':
                $filtered = $filtered->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE);
                break;
            case 'name_desc':
                $filtered = $filtered->sortByDesc('name', SORT_NATURAL | SORT_FLAG_CASE);
                break;
            default:
                // default order = newest
                $filtered = $filtered->sortByDesc('created_at');
        }

        // 4) Sidebar data (from ALL data so filters don't hide options)
        $categories = $all->pluck('category')->unique()->values();
        $brands     = $all->pluck('brand')->filter()->unique()->sort()->values();

        // 5) Pagination (manual, because we used Collections)
        $perPage = 12; // feel free to adjust
        $page    = (int) ($request->get('page', 1));
        $total   = $filtered->count();
        $items   = $filtered->slice(($page - 1) * $perPage, $perPage)->values();

        $products = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('catalogue', compact('products', 'categories', 'brands'));
    }
}
