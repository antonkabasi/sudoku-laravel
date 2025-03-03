@extends('layouts.app')

@section('title', 'Leaderboard')

@section('content')
<div class="container">
    <h1 class="text-2xl font-bold mb-4">Leaderboard</h1>

    <a href="{{ route('leaderboard.create') }}" class="btn btn-primary mb-4">Add New Score</a>

    <table class="min-w-full table-auto">
        <thead>
            <tr>
                <th class="px-4 py-2">Player Name</th>
                <th class="px-4 py-2">Difficulty</th>
                <th class="px-4 py-2">Time (s)</th>
                <th class="px-4 py-2">Date Achieved</th>
                <th class="px-4 py-2">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($entries as $entry)
            <tr>
                <td class="border px-4 py-2">{{ $entry->player_name }}</td>
                <td class="border px-4 py-2">{{ $entry->difficulty }}</td>
                <td class="border px-4 py-2 text-center">{{ $entry->stopwatch_value }}</td>
                <td class="border px-4 py-2 text-center">{{ $entry->date_achieved->format('Y-m-d H:i') }}</td>
                <td class="border px-4 py-2 text-center">
                    <a href="{{ route('leaderboard.edit', $entry->id) }}" class="btn btn-warning">Edit</a>
                    <form action="{{ route('leaderboard.destroy', $entry->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Delete this score?')">Delete</button>
                    </form>                    
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
