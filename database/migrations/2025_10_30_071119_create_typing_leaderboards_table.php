<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypingLeaderboardsTable extends Migration
{
    public function up()
    {
        Schema::create('typing_leaderboards', function (Blueprint $table) {
            $table->id();
            $table->string('nickname', 40);
            $table->string('level', 20)->index();
            $table->decimal('wpm', 8, 2);
            $table->integer('time_seconds');
            $table->decimal('accuracy', 5, 2); // percent
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('typing_leaderboards');
    }
}
