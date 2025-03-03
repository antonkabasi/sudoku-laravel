<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaderboardEntry;

class LeaderboardController extends Controller
{
    // Display a list of leaderboard entries, optionally filtering by difficulty.
    public function index(Request $request)
    {
        $difficulty = $request->query('difficulty');
        $entries = LeaderboardEntry::query();

        if ($difficulty) {
            $entries->where('difficulty', 'LIKE', $difficulty);
        }

        // Order by time (stopwatch_value) ascending and then by date achieved.
        $entries = $entries->orderBy('stopwatch_value')->orderBy('date_achieved')->get();

        return view('leaderboard.index', compact('entries'));
    }

    // Show form for creating a new leaderboard entry.
    public function create()
    {
        return view('leaderboard.create');
    }

    // Store a new leaderboard entry in the database.
    public function store(Request $request)
    {
        $request->validate([
            'player_name' => 'required|string|max:255',
            'difficulty' => 'required|string',
            'stopwatch_value' => 'required|integer',
        ]);

        LeaderboardEntry::create([
            'player_name' => $request->player_name,
            'difficulty' => $request->difficulty,
            'stopwatch_value' => $request->stopwatch_value,
            'date_achieved' => now(),
        ]);

        return redirect()->route('leaderboard.index');
    }

    // Show form to edit an existing leaderboard entry.
    public function edit($id)
    {
        $entry = LeaderboardEntry::findOrFail($id);
        return view('leaderboard.edit', compact('entry'));
    }

    // Update an existing leaderboard entry.
    public function update(Request $request, $id)
    {
        $entry = LeaderboardEntry::findOrFail($id);
        $request->validate([
            'player_name' => 'required|string|max:255',
            'difficulty' => 'required|string',
            'stopwatch_value' => 'required|integer',
        ]);

        $entry->update($request->only('player_name', 'difficulty', 'stopwatch_value'));
        return redirect()->route('leaderboard.index');
    }

    // Delete an existing leaderboard entry.
    public function destroy($id)
    {
        $entry = LeaderboardEntry::findOrFail($id);
        $entry->delete();
        return redirect()->route('leaderboard.index');
    }
}
