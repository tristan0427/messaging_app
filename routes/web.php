<?php

use App\Http\Controllers\Frontend_Controller;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\FriendController;

//Route::get('/', function () {
//    return Inertia::render('Welcome');
//})->name('home');

Route::get('/', [Frontend_Controller::class, 'index'])->name('home');
Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//<--user online-->

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/friends/list', [FriendController::class, 'list'])->name('friends.list');
});


require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
