<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaderboardEntry extends Model
{
    use HasFactory;

    // If you want to allow mass assignment, define the fillable attributes.
    protected $fillable = [
        'player_name',
        'difficulty',
        'stopwatch_value',
        'date_achieved'
    ];

    // Optionally, you can define casts if needed:
    protected $casts = [
        'date_achieved' => 'datetime',
    ];
}
