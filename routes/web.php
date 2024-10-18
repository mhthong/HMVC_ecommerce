<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MenuMainController;
use App\Http\Controllers\MenuController;
use UniSharp\LaravelFilemanager\Lfm;
use Illuminate\Http\Request;


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

// Authentication Routes


Route::get('/', function () {
    $customMessage = 'Hello, this is a custom message!';

    return Inertia::render('Welcome', [
        'customMessage' => $customMessage,
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});



Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');



Route::get('/test', function () {
    return Inertia::render('Home/Test');
})->name('test');

Route::middleware('auth')->prefix('dashboard')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    
    Route::middleware('admin')->group(function () {

        Route::prefix('settings')->group(function () {
            Route::get('/general', [SettingController::class, 'index'])->name('general.index');
            Route::post('/general', [SettingController::class, 'store'])->name('general.store');
            Route::get('/email', [SettingController::class, 'email'])->name('email.index');
            Route::get('/theme', [SettingController::class, 'theme'])->name('theme.index');
        });

        Route::prefix('menu-manager')->group(function () {
            Route::get('/', [MenuMainController::class, 'index'])->name('menu_manager.index');
            Route::get('/create', [MenuMainController::class, 'create'])->name('menu_manager.create');
            Route::post('/create', [MenuMainController::class, 'store'])->name('menu_manager.store');
            Route::get('/update/{menu}', [MenuMainController::class, 'edit'])->name('menu_manager.edit');
            Route::put('/update/{menu}', [MenuMainController::class, 'update'])->name('menu_manager.update');
            Route::delete('/delete/{menu}', [MenuMainController::class, 'destroy'])->name('menu_manager.destroy');
        });


        Route::prefix('/menu-items-manager')->group(function () {
            Route::post('/createorupdate', [MenuController::class, 'store'])->name('menu_items_manager.store');
            Route::delete('/delete/{slider}', [MenuController::class, 'destroy'])->name('menu_items_manager.destroy');
        });

        Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
            Lfm::routes();
        });

        Route::post('/file-manager/callback', [FileManagerController::class, 'handleCallback']);

    });


    Route::middleware('admin')->group(function () {
        Route::get('/auth', [UserController::class, 'index'])->name('auth.index');
        Route::post('/auth/create', [UserController::class, 'store'])->name('auth.store');       
        Route::put('/auth/update/{user}', [UserController::class, 'update'])->name('auth.update');
        Route::delete('/auth/delete/{user}', [UserController::class, 'destroy'])->name('auth.destroy');

    });






    // routes/web.php



});


use App\Http\Controllers\ContactController;

Route::post('/send-mail', [ContactController::class, 'sendContactForm'])->name('email.test');


require __DIR__ . '/auth.php';
