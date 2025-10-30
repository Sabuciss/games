<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypingLeaderboard extends Model
{
    protected $table = 'typing_leaderboards';

    protected $fillable = [
        'nickname',
        'level',
        'wpm',
        'time_seconds',
        'accuracy',
    ];
}
