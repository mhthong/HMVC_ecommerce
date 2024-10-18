<?php

use Modules\Post\Http\Controllers\PostController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Middleware\AdminMiddleware; // Import your middleware classes
use Illuminate\Auth\Middleware\EnsureEmailIsVerified; // For 'verified' middleware


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|7kỵgh `s
*/ 



Route::middleware('auth')->group(function () {


    Route::middleware('admin')->prefix('dashboard')->group(function () {

        Route::prefix('post-manager')->group(function () {
            Route::get('/', [PostController::class, 'index'])->name('post_manager.index');            
            Route::get('/create', [PostController::class, 'create'])->name('post_manager.create');
            Route::post('/create', [PostController::class, 'store'])->name('post_manager.store');       
            Route::get('/update/{post}', [PostController::class, 'edit'])->name('post_manager.edit');
            Route::put('/update/{post}', [PostController::class, 'update'])->name('post_manager.update');
            Route::delete('/delete/{post}', [PostController::class, 'destroy'])->name('post_manager.destroy');
        });

    });
});
