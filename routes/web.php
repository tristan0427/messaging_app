<?php

use App\Http\Controllers\Frontend_Controller;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\FriendController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\GroupController;

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
//chats controller
Route::middleware('auth')->group(function () {
    Route::get('/chat/{friendId}', [ChatController::class, 'getOrCreateChat']);
    Route::post('/chat/{chatId}/send', [ChatController::class, 'sendMessage']);
});
Route::get('/groups/list', [GroupController::class, 'index']);
Route::get('/group-chat/messages', [GroupController::class, 'getMessages']);
Route::post('/group-chat/send', [GroupController::class, 'sendMessage']);

// Add this to your routes/web.php
Route::get('/storage/Profile_Picture/{filename}', function ($filename) {
    $path = storage_path('app/public/Profile_Picture/' . $filename);

    if (!file_exists($path)) {
        abort(404);
    }

    return response()->file($path);
})->where('filename', '.*');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
