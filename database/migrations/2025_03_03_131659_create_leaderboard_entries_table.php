<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('leaderboard_entries', function (Blueprint $table) {
            $table->id();
            $table->string('player_name');
            $table->string('difficulty');
            $table->integer('stopwatch_value');
            $table->timestamp('date_achieved');
        });
    }

    public function down()
    {
        Schema::dropIfExists('leaderboard_entries');
    }

};
