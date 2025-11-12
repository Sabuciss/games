<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardResult extends Model
{
    protected $table = 'cardresults';

    protected $fillable = ['nickname', 'level', 'score', 'time_seconds'];
}

