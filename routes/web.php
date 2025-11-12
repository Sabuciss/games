<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TypingGameController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/play', function () {
    return view('cards.play');
});

Route::get('/typing-game', [TypingGameController::class, 'index'])->name('typing.index');
Route::post('/typing-game/submit', [TypingGameController::class, 'submit'])->name('typing.submit');
Route::get('/typing-game/leaderboard/{level}', [TypingGameController::class, 'leaderboard']);


