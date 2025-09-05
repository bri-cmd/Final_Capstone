<?php

use App\Http\Controllers\BuildController;
use App\Http\Controllers\BuildExtController;
use App\Http\Controllers\ComponentDetailsController;
use App\Http\Controllers\Components\CaseController;
use App\Http\Controllers\Components\CoolerController;
use App\Http\Controllers\Components\CpuController;
use App\Http\Controllers\Components\GpuController;
use App\Http\Controllers\Components\MoboController;
use App\Http\Controllers\Components\PsuController;
use App\Http\Controllers\Components\RamController;
use App\Http\Controllers\Components\StorageController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\UserAccountController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CatalogueController;
use App\Http\Controllers\CartController;

Route::get('/', function () {
    return view('welcome');
})->name('home');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');


require __DIR__.'/auth.php';

Route::get('techboxx/build', [BuildController::class, 'index'])->name('techboxx.build');
Route::get('techboxx/build/generate', [BuildController::class, 'generate'])->name('techboxx.generate');
Route::post('techboxx/build/validate', [BuildController::class, 'validateBuild'])->name('techboxx.validate');
Route::get('techboxx/build-extended', [BuildExtController::class, 'index'])->name('techboxx.build.extend');

// ADMIN ROUTES
Route::prefix('admin')->name('admin.')->group(function () {
    // DASHBOARD
    Route::get('dashboard', [UserAccountController::class, 'index'])->name('dashboard');

    // USER ACCOUNT
    Route::get('user-account', [UserAccountController::class, 'useraccount'])->name('useraccount');
    Route::post('dashboard', [UserAccountController::class, 'store'])->name('users.add');
    Route::post('users/{id}/approve', [UserAccountController::class, 'approve'])->name('users.approved');
    Route::delete('users/{id}/decline', [UserAccountController::class, 'decline'])->name('users.decline');
    Route::put('users/{id}/update', [UserAccountController::class, 'update']);
    Route::delete('users/{id}/delete', [UserAccountController::class, 'delete'])->name('users.delete');

    // SALES
    Route::get('sales', [SalesController::class, 'index'])->name('sales');
});

// STAFF ROUTES
Route::prefix('staff')->name('staff.')->group(function () {
    //DASHBOARD
    Route::get('dashboard', [UserAccountController::class, 'index'])->name('dashboard');

    // COMPONENT DETAILS
    Route::get('component-details', [ComponentDetailsController::class, 'index'])->name('componentdetails');
    Route::get('component-details/search', [ComponentDetailsController::class, 'search'])->name('componentdetails.search');
    Route::post('component-details/motherboard', [MoboController::class, 'store'])->name('componentdetails.motherboard.store');
    Route::post('component-details/gpu', [GpuController::class, 'store'])->name('componentdetails.gpu.store');
    Route::post('component-details/case', [CaseController::class, 'store'])->name('componentdetails.case.store');
    Route::post('component-details/psu', [PsuController::class, 'store'])->name('componentdetails.psu.store');
    Route::post('component-details/ram', [RamController::class, 'store'])->name('componentdetails.ram.store');
    Route::post('component-details/storage', [StorageController::class, 'store'])->name('componentdetails.storage.store');
    Route::post('component-details/cpu', [CpuController::class, 'store'])->name('componentdetails.cpu.store');
    Route::post('component-details/cooler', [CoolerController::class, 'store'])->name('componentdetails.cooler.store');
    Route::put('component-details/psu/{id}', [PsuController::class, 'update']);
    Route::put('component-details/storage/{id}', [StorageController::class, 'update']);
    Route::put('component-details/case/{id}', [CaseController::class, 'update']);
    Route::put('component-details/cpu/{id}', [CpuController::class, 'update']);
    Route::put('component-details/motherboard/{id}', [MoboController::class, 'update']);
    Route::put('component-details/gpu/{id}', [GpuController::class, 'update']);
    Route::put('component-details/ram/{id}', [RamController::class, 'update']);
    Route::put('component-details/cooler/{id}', [CoolerController::class, 'update']);
    Route::delete('component-details/{type}/{id}', [ComponentDetailsController::class, 'delete'])->name('componentdetails.delete');

    // INVENTORY REPORT
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::get('inventory/search', [InventoryController::class, 'search'])->name('inventory.search');
    Route::post('inventory/stock-in', [InventoryController::class, 'stockIn'])->name('inventory.stock-in');
    Route::post('inventory/stock-out', [InventoryController::class, 'stockOut'])->name('inventory.stock-out');

    
});

// CUSTOMER ROUTES
Route::prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard',[CustomerController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
    Route::put('profile', [CustomerController::class, 'update'])->name('profile.update');
});

Route::resource('cpus', CpuController::class);

// catalogue routes
Route::get('/catalogue', [CatalogueController::class, 'index'])->name('catalogue');

Route::get('/cart', [CartController::class, 'index'])->name('cart');
Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');