<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeaderboardEntriesTable extends Migration
{
    public function up()
    {
        Schema::create('leaderboard_entries', function (Blueprint $table) {
            $table->id();
            $table->string('player_name');
            $table->string('difficulty'); // e.g., Easy, Medium, Hard
            $table->integer('stopwatch_value'); // time in seconds
            $table->timestamp('date_achieved')->useCurrent();
            $table->timestamps(); // optional, for created_at and updated_at
        });
    }

    public function down()
    {
        Schema::dropIfExists('leaderboard_entries');
    }
}
