<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypingLeaderboard extends Model
{
    use HasFactory;

    protected $fillable = [
        'nickname',
        'level',
        'wpm',
        'time_seconds',
        'accuracy',
    ];
}

