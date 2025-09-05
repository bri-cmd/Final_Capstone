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
        /* Match header height */
        body {
            padding-top: 80px; /* same as h-20 (20 * 4px = 80px) */
        }
    </style>
</head>
<body class="bg-gray-100">

    <!-- Fixed landing header -->
    <div class="fixed top-0 left-0 w-full z-50 h-20 bg-white shadow">
        <x-landingheader :name="Auth::user()?->first_name" />
    </div>

    <!-- Top Nav Tabs -->
    <div class="w-full border-b bg-white shadow-sm">
        <div class="flex justify-center items-center gap-8 py-4 text-gray-600 font-semibold text-sm">
            <a href="{{ route('catalogue') }}" class="hover:underline hover:text-blue-500">ALL</a>
            <a href="{{ route('catalogue', ['filter' => 'new']) }}" class="hover:underline hover:text-blue-500">NEW IN</a>
            <a href="{{ route('catalogue', ['filter' => 'hot']) }}" class="hover:underline hover:text-blue-500">HOT</a>
            <a href="{{ route('catalogue', ['filter' => 'recent']) }}" class="hover:underline hover:text-blue-500">RECENT</a>
            <a href="{{ route('catalogue', ['filter' => 'popular']) }}" class="hover:underline hover:text-blue-500">POPULAR</a>
        </div>
    </div>

    <!-- Search + Sort -->
    <div class="w-full flex justify-end items-center px-8 py-4 border-b bg-white">
        <form method="GET" action="{{ route('catalogue') }}" class="flex items-center gap-2 max-w-lg w-full">
            <div class="relative flex-1">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">🔍</span>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Search for items or categories" 
                       class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-full bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-200 shadow-sm transition placeholder-gray-400 text-sm">
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
        <aside class="w-1/4 p-6 border-r bg-white shadow overflow-y-auto">
            <h2 class="font-bold mb-3">CATEGORY</h2>
            <ul class="text-sm space-y-1">
                <li><a href="{{ route('catalogue', ['category' => 'Pre-Built PCs']) }}" class="hover:underline">Pre-Built PCs</a></li>
                <li>
                    <a href="{{ route('catalogue', ['category' => 'Components']) }}" class="hover:underline">Components</a>
                    <ul class="ml-4 space-y-1">
                        <li><a href="{{ route('catalogue', ['category' => 'Motherboard']) }}" class="hover:underline">Motherboards</a></li>
                        <li><a href="{{ route('catalogue', ['category' => 'CPU']) }}" class="hover:underline">CPUs</a></li>
                        <li><a href="{{ route('catalogue', ['category' => 'RAM']) }}" class="hover:underline">RAM</a></li>
                        <li><a href="{{ route('catalogue', ['category' => 'Storage']) }}" class="hover:underline">Storage</a></li>
                        <li><a href="{{ route('catalogue', ['category' => 'PSU']) }}" class="hover:underline">Power Supply</a></li>
                        <li><a href="{{ route('catalogue', ['category' => 'Cooling Systems']) }}" class="hover:underline">Cooling Systems</a></li>
                        <li><a href="{{ route('catalogue', ['category' => 'Cases']) }}" class="hover:underline">Cases</a></li>
                    </ul>
                </li>
            </ul>

            <!-- Price Filter -->
            <h2 class="font-bold mt-6 mb-2">PRICE</h2>
            <form method="GET" action="{{ route('catalogue') }}" class="space-y-3">
                <div class="flex justify-between text-sm text-gray-600">
                    <span>₱1,000</span>
                    <span>₱200,000</span>
                </div>

                <div class="flex gap-2">
                    <input type="number" name="min_price" placeholder="Min ₱" 
                           value="{{ request('min_price') }}" 
                           min="1000" max="200000" step="500"
                           class="border p-1 w-24 rounded">
                    <input type="number" name="max_price" placeholder="Max ₱" 
                           value="{{ request('max_price') ?? 200000 }}" 
                           id="maxPriceInput"
                           min="1000" max="200000" step="500"
                           class="border p-1 w-24 rounded">
                </div>

                <input type="range" name="max_price" min="1000" max="200000" step="500"
                       value="{{ request('max_price') ?? 200000 }}"
                       id="priceSlider"
                       class="w-full h-2 bg-gray-300 rounded-lg cursor-pointer accent-blue-500">

                <p class="text-sm text-gray-700">Up to: ₱<span id="priceValue">{{ request('max_price') ?? 200000 }}</span></p>

                <button type="submit" class="w-full px-3 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
                    Apply Filter
                </button>
            </form>

            <!-- Brands -->
            <h2 class="font-bold mt-6 mb-2">BRAND</h2>
            <ul class="text-sm space-y-1">
                @foreach($brands as $brand)
                    <li>
                        <a href="{{ route('catalogue', ['brand' => $brand]) }}" class="hover:underline">
                            {{ $brand }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="mt-6">
                <a href="{{ route('catalogue') }}" class="block text-center px-3 py-2 bg-blue-500 text-white text-sm rounded hover:bg-blue-600">
                    Clear All Filters
                </a>
            </div>

            <div class="mt-6">
                <a href="" class="block text-center px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    + Add Product
                </a>
            </div>
        </aside>

        <!-- Product Grid -->
        <main class="w-3/4 p-6 grid grid-cols-4 gap-6">
            @forelse($products as $product)
                <div x-data="{ openModal: false }" class="relative border rounded-lg p-4 text-center bg-blue-50 shadow hover:shadow-lg transition flex flex-col justify-between h-[320px]">
                     <!-- Three-dot menu button -->
                    <button @click="openModal = true"
                            class="absolute top-2 right-2 p-2 rounded-full bg-white shadow hover:bg-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" 
                            fill="currentColor" 
                            viewBox="0 0 16 16" 
                            class="w-5 h-5 text-gray-700">
                            <path d="M3 9.5a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3z"/>
                        </svg>
                    </button>
                    
                    <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/placeholder.png') }}" 
                         class="mx-auto mb-3 h-32 object-contain">

                    <div>
                        <h3 class="font-bold text-sm truncate" title="{{ $product->name }}">{{ $product->name }}</h3>
                        <p class="text-xs text-gray-600">{{ $product->brand }}</p>
                    </div>

                    <p class="text-yellow-500 text-sm mt-1">
                        @if($product->rating)
                            ⭐ {{ $product->rating }} ({{ $product->reviews_count }})
                        @else
                            ⭐ No reviews yet
                        @endif
                    </p>

                    <p class="text-gray-800 font-semibold">₱{{ number_format($product->price, 0) }}</p>

                    <form action="{{ route('cart.add') }}" method="POST" class="mt-auto">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <button type="submit" class="w-full py-2 bg-white border rounded-md font-semibold text-gray-700 shadow hover:bg-gray-100">
                            Add to Cart
                        </button>
                    </form>
                    <template x-if="openModal">
            <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white rounded-xl shadow-lg p-6 w-96 relative">
                    <button @click="openModal = false"
                            class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">✕</button>

                    <h2 class="text-lg font-bold mb-2">Component Specs</h2>
                    <hr class="mb-4">

                    <p><strong>Component:</strong> {{ $product->name }}</p>
                    <p><strong>Category:</strong> {{ $product->category }}</p>
                    <p><strong>Cores/Threads:</strong> {{ $product->cores_threads }}</p>
                    <p><strong>Base Clock:</strong> {{ $product->base_clock }}</p>
                    <p><strong>Socket:</strong> {{ $product->socket }}</p>
                    <p><strong>Price:</strong> ₱{{ number_format($product->price, 0) }}</p>
                    <p><strong>Stock:</strong> Available ({{ $product->stock }} units)</p>
                </div>
            </div>
        </template>
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
