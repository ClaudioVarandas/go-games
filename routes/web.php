<?php

use App\Http\Controllers\GameController;
use App\Http\Controllers\GameListController;
use App\Http\Controllers\GameListItemController;
use App\Http\Controllers\GameStatusController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Game routes
Route::get('/', [GameController::class, 'index'])->name('home');
Route::get('/search', [GameController::class, 'search'])->name('games.search');
Route::get('/games/{slug}', [GameController::class, 'show'])->name('games.show');

// Redirect dashboard to home
Route::redirect('dashboard', '/');

Route::middleware(['auth', 'verified'])->group(function () {
    // My Games page
    Route::get('my-games', [GameStatusController::class, 'index'])->name('my-games');

    // Game Lists
    Route::resource('game-lists', GameListController::class);
    
    // Game List Items
    Route::post('game-lists/{gameList}/games', [GameListItemController::class, 'store'])->name('game-lists.games.store');
    Route::delete('game-lists/{gameList}/games/{game}', [GameListItemController::class, 'destroy'])->name('game-lists.games.destroy');
    
    // Game Statuses
    Route::post('games/{game}/status', [GameStatusController::class, 'store'])->name('games.status.store');
    Route::delete('games/{game}/status', [GameStatusController::class, 'destroy'])->name('games.status.destroy');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
