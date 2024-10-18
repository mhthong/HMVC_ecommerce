<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\ProductController;
use Modules\Product\Http\Controllers\CategoryController;
use Modules\Product\Http\Controllers\DiscountsController;
use Modules\Product\Http\Controllers\WarantyController;

/*
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::middleware('auth')->group(function () {

    Route::middleware('admin')->prefix('dashboard')->group(function () {
        Route::prefix('product-manager')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('product_manager.index');
            Route::get('/create', [ProductController::class, 'create'])->name('product_manager.create');
            Route::post('/create', [ProductController::class, 'store'])->name('product_manager.store');
            Route::get('/update/{product}', [ProductController::class, 'edit'])->name('product_manager.edit');
            Route::put('/update/{product}', [ProductController::class, 'update'])->name('product_manager.update');
            Route::delete('/delete/{product}', [ProductController::class, 'destroy'])->name('product_manager.destroy');
            Route::post('/bulk-delete', [ProductController::class, 'bulkDestroy'])->name('products.bulkDestroy');
        });

        Route::prefix('discounts-manager')->group(function () {
            Route::get('/', [DiscountsController::class, 'index'])->name('discounts_manager.index');
            Route::post('/create', [DiscountsController::class, 'store'])->name('discounts_manager.store');
            Route::put('/update/{discount}', [DiscountsController::class, 'update'])->name('discounts_manager.update');
            Route::delete('/delete/{discount}', [DiscountsController::class, 'destroy'])->name('discounts_manager.destroy');
        });

        Route::prefix('waranty-manager')->group(function () {
            Route::get('/', [WarantyController::class, 'index'])->name('waranty_manager.index');
            Route::get('/create', [WarantyController::class, 'create'])->name('waranty_manager.create');
            Route::get('/{id}', [WarantyController::class, 'floder'])->name('waranty_manager.floder');
            Route::put('/clear/{product_id}', [WarantyController::class, 'clear'])->name('waranty_manager.clear');
            Route::post('/create', [WarantyController::class, 'store'])->name('waranty_manager.store');
        });


        Route::prefix('/category-items-manager')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('category_items_manager.index');
            Route::post('/create', [CategoryController::class, 'store'])->name('category_items_manager.store');
            Route::put('/update/{category}', [CategoryController::class, 'update'])->name('category_items_manager.update');
            Route::delete('/delete/{category}', [CategoryController::class, 'destroy'])->name('category_items_manager.destroy');
        });
    });
});

Route::get('thong-tin-bao-hanh/{warranty_code}', [WarantyController::class, 'warranty'])->name('warranty.index');