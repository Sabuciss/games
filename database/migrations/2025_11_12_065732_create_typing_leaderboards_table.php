<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('typing_leaderboards', function (Blueprint $table) {
        $table->id();
        $table->string('nickname', 40);
        $table->string('level'); // easy, medium, hard, hardcore
        $table->float('wpm');
        $table->integer('time_seconds');
        $table->float('accuracy');
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('typing_leaderboards');
    }
};
