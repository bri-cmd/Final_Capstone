<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');              // Product name (e.g. AMD Ryzen 5 5600)
            $table->string('brand')->nullable(); // Brand (AMD, ASUS, Corsair, etc.)
            $table->string('category');          // Category (CPU, Motherboard, RAM, PSU, etc.)
            $table->integer('price');            // Price (₱8000, etc.)
            $table->string('image');             // Path to product image
            $table->float('rating')->default(0); // Average rating (e.g. 5.0)
            $table->integer('reviews_count')->default(0); // Number of reviews (e.g. 3)
            $table->timestamps();                // Created_at & Updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
