<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/play', function () {
    return view('cards.play');
});

Route::get('/typing-game', [App\Http\Controllers\TypingGameController::class, 'index'])->name('typing.index');
Route::post('/typing-game/submit', [App\Http\Controllers\TypingGameController::class, 'submit'])->name('typing.submit');
Route::get('/typing-game/leaderboard/{level}', [App\Http\Controllers\TypingGameController::class, 'leaderboard'])->name('typing.leaderboard');
