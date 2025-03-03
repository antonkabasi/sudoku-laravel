@extends('layouts.app')

@section('title', 'Submit Score')

@section('content')
<div class="container mx-auto px-4">
    <h1 class="text-2xl font-bold mb-4">Submit Your Score</h1>

    <form action="{{ route('leaderboard.store') }}" method="POST" class="max-w-md mx-auto">
        @csrf
        <div class="mb-4">
            <label for="player_name" class="block text-gray-700 font-bold mb-1">Player Name</label>
            <input type="text" id="player_name" name="player_name" value="{{ old('player_name') }}"
                   class="w-full border rounded p-2" required>
            @error('player_name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="difficulty" class="block text-gray-700 font-bold mb-1">Difficulty</label>
            <select id="difficulty" name="difficulty" class="w-full border rounded p-2" required>
                <option value="Easy" {{ old('difficulty') == 'Easy' ? 'selected' : '' }}>Easy</option>
                <option value="Normal" {{ old('difficulty') == 'Normal' ? 'selected' : '' }}>Normal</option>
                <option value="Hard" {{ old('difficulty') == 'Hard' ? 'selected' : '' }}>Hard</option>
            </select>
            @error('difficulty')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <div class="mb-4">
            <label for="stopwatch_value" class="block text-gray-700 font-bold mb-1">Time (in seconds)</label>
            <input type="number" id="stopwatch_value" name="stopwatch_value" value="{{ old('stopwatch_value') }}"
                   class="w-full border rounded p-2" required>
            @error('stopwatch_value')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary">
            Submit Score
        </button>
    </form>
</div>
@endsection
