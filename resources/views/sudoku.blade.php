@extends('layouts.app')

@section('title', 'Sudoku Game')

@section('content')
<div class="container text-center">
    <h1>Sudoku Game</h1>

    <!-- Difficulty Selection -->
    <div class="d-flex justify-content-center my-3">
        <a class="btn btn-primary mx-2" href="{{ route('home', ['difficulty' => 'easy']) }}">Easy</a>
        <a class="btn btn-primary mx-2" href="{{ route('home', ['difficulty' => 'medium']) }}">Normal</a>
        <a class="btn btn-primary mx-2" href="{{ route('home', ['difficulty' => 'hard']) }}">Hard</a>
    </div>

    <!-- Flash Messages -->
    @if(session('message'))
        <div id="messageContainer" class="alert alert-info text-center">{{ session('message') }}</div>
    @endif

    <div id="autoMessage" class="text-center text-bold"></div>

    <!-- Sudoku Board -->
    <form action="{{ route('check') }}" method="POST">
        @csrf
        <table class="mx-auto">
            @for ($i = 0; $i < 9; $i++)
                <tr>
                    @for ($j = 0; $j < 9; $j++)
                        @php
                            $borderTop = ($i % 3 == 0) ? '3px solid black' : '1px solid black';
                            $borderLeft = ($j % 3 == 0) ? '3px solid black' : '1px solid black';
                            $borderRight = ($j == 8) ? '3px solid black' : '1px solid black';
                            $borderBottom = ($i == 8) ? '3px solid black' : '1px solid black';
                            $givenValue = $sudoku->Board[$i][$j] ?? 0;
                            $cellValue = ($givenValue != 0) ? $givenValue : '';
                        @endphp
                        <td data-row="{{ $i }}" data-col="{{ $j }}" style="width:50px; height:50px; border-top:{{ $borderTop }}; border-left:{{ $borderLeft }}; border-right:{{ $borderRight }}; border-bottom:{{ $borderBottom }}; padding:0;">
                            <input type="text" name="cell_{{ $i }}_{{ $j }}" class="form-control text-center p-0 sudoku-cell {{ $givenValue != 0 ? 'given' : '' }}" maxlength="1"
                                   value="{{ $cellValue }}" style="height: 100%; border: none;" {{ $givenValue != 0 ? 'readonly' : '' }} />
                        </td>
                    @endfor
                </tr>
            @endfor
        </table>

        <div class="text-center mt-3">
            <button type="submit" class="btn btn-success">Check</button>
            <button type="submit" formaction="{{ route('solve') }}" class="btn btn-danger ml-2">Solve</button>
        </div>
    </form>

    <!-- Stopwatch -->
    <div class="text-center mt-3">
        <strong>Time:</strong> <span id="stopwatch">00:00</span>
    </div>
</div>

<!-- JavaScript for Auto-Check & Stopwatch -->
<script>
    async function updateStopwatch() {
        try {
            const response = await fetch('{{ route("stopwatch") }}');
            const data = await response.json();
            let secondsElapsed = data.elapsed;
            const minutes = Math.floor(secondsElapsed / 60);
            const seconds = secondsElapsed % 60;
            document.getElementById('stopwatch').textContent =
                (minutes < 10 ? "0" + minutes : minutes) + ":" +
                (seconds < 10 ? "0" + seconds : seconds);
        } catch (error) {
            console.error('Error fetching stopwatch time:', error);
        }
    }
    setInterval(updateStopwatch, 1000);
    updateStopwatch();
</script>
@endsection
