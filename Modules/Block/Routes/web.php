<?php

use Illuminate\Support\Facades\Route;
use Modules\Block\Http\Controllers\BlockController;
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

        Route::prefix('block-manager')->group(function () {
            Route::get('/', [BlockController::class, 'index'])->name('block_manager.index');            
            Route::get('/create', [BlockController::class, 'create'])->name('block_manager.create');
            Route::post('/create', [BlockController::class, 'store'])->name('block_manager.store');       
            Route::get('/update/{block}', [BlockController::class, 'edit'])->name('block_manager.edit');
            Route::put('/update/{block}', [BlockController::class, 'update'])->name('block_manager.update');
            Route::delete('/delete/{block}', [BlockController::class, 'destroy'])->name('block_manager.destroy');
        });

    });
});
