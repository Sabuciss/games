<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemoryController;
use App\Http\Controllers\TypingGameController;

// Sākumlapa
Route::get('/', function () {
    return view('welcome');
});

// Atmiņas spēle
Route::get('/play', [MemoryController::class, 'showPlay'])->name('memory.play');

// Typing-game routes
Route::get('/typing-game', [TypingGameController::class, 'index'])->name('typing.index');
Route::post('/typing-game/submit', [TypingGameController::class, 'submit'])->name('typing.submit');
Route::get('/typing-game/leaderboard/{level}', [TypingGameController::class, 'leaderboard'])->name('typing.leaderboard');

// Memory game highscore submit & leaderboard
Route::post('/memory/submit-result', [MemoryController::class, 'submitResult'])->name('memory.submitResult');
Route::get('/cardleaderboard', [MemoryController::class, 'showLeaderboardWithPlayer'])->name('cardleaderboard');

// Logout no Memory spēles
Route::post('/memory/logout', [MemoryController::class, 'logout'])->name('memory.logout');
