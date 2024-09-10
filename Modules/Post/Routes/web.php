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

Route::prefix('post')->middleware(['admin', 'verified'])->group(function() {
    Route::get('/', [PostController::class, 'index'])->name('post');    
    
    Route::get('/test', function () { 
        return Inertia::render('Home/Test');
    })->name('test');

}); 