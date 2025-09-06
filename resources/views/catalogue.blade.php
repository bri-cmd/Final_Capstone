<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue - Techboxx</title>

    @vite([
        'resources/css/app.css',
        'resources/css/landingpage/header.css',
        'resources/js/app.js'
    ])

    <style>
        /* Match fixed header height */
        body { padding-top: 80px; } /* h-20 */
    </style>
</head>
<body class="bg-gray-100">

    <!-- Fixed landing header -->
    <div class="fixed top-0 left-0 w-full z-50 h-20 bg-white shadow">
        <x-landingheader :name="Auth::user()?->first_name" />
    </div>

    <!-- Top Nav Tabs (optional filter shortcuts) -->
    <div class="w-full border-b bg-white shadow-sm">
        <div class="flex justify-center items-center gap-8 py-4 text-gray-600 font-semibold text-sm">
            <a href="{{ route('catalogue') }}" class="hover:underline hover:text-blue-500">ALL</a>
            <a href="{{ route('catalogue', ['sort' => 'newest']) }}" class="hover:underline hover:text-blue-500">NEW IN</a>
            <a href="{{ route('catalogue', ['sort' => 'price_desc']) }}" class="hover:underline hover:text-blue-500">HOT</a>
            <a href="{{ route('catalogue', ['sort' => 'newest']) }}" class="hover:underline hover:text-blue-500">RECENT</a>
            <a href="{{ route('catalogue', ['sort' => 'name_asc']) }}" class="hover:underline hover:text-blue-500">POPULAR</a>
        </div>
    </div>

    <!-- Search + Sort -->
    <div class="w-full flex justify-end items-center px-8 py-4 border-b bg-white">
        <form method="GET" action="{{ route('catalogue') }}" class="flex items-center gap-2 max-w-lg w-full">
            <div class="relative flex-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">🔍</span>
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search for items or categories"
                    class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-full bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-200 shadow-sm transition placeholder-gray-400 text-sm"
                >
            </div>

            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif

            <select name="sort" onchange="this.form.submit()"
                class="ml-2 px-4 py-2 border border-gray-200 rounded-full bg-white text-gray-700 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-200">
                <option value="">Sort: Default</option>
                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Sort: New</option>
                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name: A-Z</option>
                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name: Z-A</option>
            </select>
        </form>
    </div>

    <div class="min-h-screen bg-gray-100 flex">

        <!-- Sidebar -->
        <aside class="w-full sm:w-1/4 p-6 border-r bg-white shadow overflow-y-auto">
            <h2 class="font-bold mb-3">CATEGORY</h2>
            <ul class="text-sm space-y-1">
                @forelse($categories as $cat)
                    <li>
                        <a href="{{ route('catalogue', array_merge(request()->except('page'), ['category' => $cat])) }}"
                           class="hover:underline {{ request('category') === $cat ? 'text-blue-600 font-semibold' : '' }}">
                            {{ $cat }}
                        </a>
                    </li>
                @empty
                    <li class="text-gray-400">No categories</li>
                @endforelse
            </ul>

            <!-- Price Filter -->
            <h2 class="font-bold mt-6 mb-2">PRICE</h2>
            <form method="GET" action="{{ route('catalogue') }}" class="space-y-3">
                {{-- keep existing query except price params --}}
                @foreach(request()->except(['min_price','max_price','page']) as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach

                <div class="flex justify-between text-sm text-gray-600">
                    <span>₱1,000</span>
                    <span>₱200,000</span>
                </div>

                <div class="flex gap-2">
                    <input type="number" name="min_price" placeholder="Min ₱"
                           value="{{ request('min_price') }}"
                           min="0" step="1"
                           class="border p-1 w-24 rounded">
                    <input type="number" name="max_price" placeholder="Max ₱"
                           value="{{ request('max_price') }}"
                           min="0" step="1"
                           class="border p-1 w-24 rounded">
                </div>

                <button type="submit" class="w-full px-3 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
                    Apply Filter
                </button>
            </form>

            <!-- Brands -->
            <h2 class="font-bold mt-6 mb-2">BRAND</h2>
            <ul class="text-sm space-y-1">
                @forelse($brands as $brand)
                    <li>
                        <a href="{{ route('catalogue', array_merge(request()->except('page'), ['brand' => $brand])) }}"
                           class="hover:underline {{ request('brand') === $brand ? 'text-blue-600 font-semibold' : '' }}">
                            {{ $brand }}
                        </a>
                    </li>
                @empty
                    <li class="text-gray-400">No brands</li>
                @endforelse
            </ul>

            <div class="mt-6">
                <a href="{{ route('catalogue') }}"
                   class="block text-center px-3 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
                    Clear All Filters
                </a>
            </div>

            <div class="mt-6">
                <a href="#" class="block text-center px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    + Add Product
                </a>
            </div>
        </aside>

        <!-- Product Grid -->
        <main class="w-full sm:w-3/4 p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse($products as $product)
                <div class="relative border rounded-lg p-4 text-center bg-blue-50 shadow hover:shadow-lg transition flex flex-col justify-between h-[320px]">
                    <img src="{{ $product['image'] }}" alt="{{ $product['name'] }}"
                         class="mx-auto mb-3 h-32 object-contain">

                    <div>
                        <h3 class="font-bold text-sm truncate" title="{{ $product['name'] }}">{{ $product['name'] }}</h3>
                        <p class="text-xs text-gray-600">{{ $product['brand'] }}</p>
                        <p class="text-[11px] text-gray-500 mt-0.5">{{ $product['category'] }}</p>
                    </div>

                    <p class="text-gray-800 font-semibold mt-1">₱{{ number_format($product['price'], 0) }}</p>

                    <form action="{{ route('cart.add') }}" method="POST" class="mt-auto">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product['id'] }}">
                        <input type="hidden" name="name" value="{{ $product['name'] }}">
                        <input type="hidden" name="price" value="{{ $product['price'] }}">
                        <button type="submit" class="w-full py-2 bg-white border rounded-md font-semibold text-gray-700 shadow hover:bg-gray-100">
                            Add to Cart
                        </button>
                    </form>


                </div>
            @empty
                <p class="col-span-4 text-center text-gray-500">No products available.</p>
            @endforelse
        </main>
    </div>

    <div class="px-6 py-4">
        {{ $products->withQueryString()->links() }}
    </div>

    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>
