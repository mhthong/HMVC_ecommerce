<?php

use Illuminate\Support\Facades\Route;
use Modules\Slider\Http\Controllers\SliderController;
use Modules\Slider\Http\Controllers\SliderItemsController;
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
        Route::prefix('slider-manager')->group(function () {
            Route::get('/', [SliderController::class, 'index'])->name('slider_manager.index');
            Route::get('/create', [SliderController::class, 'create'])->name('slider_manager.create');
            Route::post('/create', [SliderController::class, 'store'])->name('slider_manager.store');
            Route::get('/update/{slider}', [SliderController::class, 'edit'])->name('slider_manager.edit');
            Route::put('/update/{slider}', [SliderController::class, 'update'])->name('slider_manager.update');
            Route::delete('/delete/{slider}', [SliderController::class, 'destroy'])->name('slider_manager.destroy');
        });


        Route::prefix('/slider-items-manager')->group(function () {
            Route::post('/createorupdate', [SliderItemsController::class, 'store'])->name('slider_items_manager.store');
            Route::delete('/delete/{slider}', [SliderItemsController::class, 'destroy'])->name('slider_items_manager.destroy');
        });
    });
});
