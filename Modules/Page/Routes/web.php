<?php

use Illuminate\Support\Facades\Route;
use Modules\Page\Http\Controllers\PageController;
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

    Route::middleware('admin')->group(function () {

        Route::prefix('page-manager')->group(function () {
            Route::get('/', [PageController::class, 'index'])->name('page_manager.index');            
            Route::get('/create', [PageController::class, 'create'])->name('page_manager.create');
            Route::post('/create', [PageController::class, 'store'])->name('page_manager.store');       
            Route::get('/update/{page}', [PageController::class, 'edit'])->name('page_manager.edit');
            Route::put('/update/{page}', [PageController::class, 'update'])->name('page_manager.update');
            Route::delete('/delete/{page}', [PageController::class, 'destroy'])->name('page_manager.destroy');
        });

    });
});
