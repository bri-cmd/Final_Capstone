<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::create([
            'name' => 'AMD Ryzen 5 5600',
            'brand' => 'AMD',
            'category' => 'CPU',
            'price' => 8000,
            'image' => 'images/products/amd_ryzen_5_5600.jpg',
            'rating' => 4.5,
            'reviews_count' => 3,
        ]);

        Product::create([
            'name' => 'ASUS ROG Strix B550-F',
            'brand' => 'ASUS',
            'category' => 'Motherboard',
            'price' => 12000,
            'image' => 'images/products/asus_rog_strix_b550_f.jpg',
            'rating' => 4.7,
            'reviews_count' => 5,
        ]);

        Product::create([
            'name' => 'Corsair Vengeance LPX 16GB',
            'brand' => 'Corsair',
            'category' => 'RAM',
            'price' => 4000,
            'image' => 'images/products/corsair_vengeance_lpx_16gb.jpg',
            'rating' => 4.8,
            'reviews_count' => 10,
        ]);

        Product::create([
            'name' => 'Seasonic Focus GX-750',
            'brand' => 'Seasonic',
            'category' => 'PSU',
            'price' => 6000,
            'image' => 'images/products/seasonic_focus_gx_750.jpg',
            'rating' => 4.6,
            'reviews_count' => 8,
        ]);
    }
}
