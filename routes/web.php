<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\FileManagerController;
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

Route::middleware('auth')->group(function () {
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


        Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
            Lfm::routes();
        });

        Route::post('/file-manager/callback', [FileManagerController::class, 'handleCallback']);

    });

    // routes/web.php



});


use App\Http\Controllers\ContactController;

Route::post('/send-mail', [ContactController::class, 'sendContactForm'])->name('email.test');


require __DIR__ . '/auth.php';
