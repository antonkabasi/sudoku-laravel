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

    <!-- ✅ Game Result Box (Initially Hidden) -->
    <div id="gameResult" class="alert text-center mt-3 d-none"></div>

    <!-- Sudoku Board -->
    <form id="sudokuForm" action="{{ route('check') }}" method="POST">
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
            <button type="submit" class="btn btn-success" id="checkButton">Check</button>
            <button type="button" class="btn btn-danger ml-2" id="solveButton">Solve</button>
        </div>
    </form>

    <!-- Stopwatch -->
    <div class="text-center mt-3">
        <strong>Time:</strong> <span id="stopwatch">00:00</span>
    </div>
</div>

<!-- JavaScript for AJAX Check & Solve -->
<script>
   let elapsedSeconds = 0; // Stores the elapsed time from the server

    async function fetchStopwatchTime() {
        try {
            const response = await fetch('{{ route("stopwatch") }}');

            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }

            const data = await response.json();
            elapsedSeconds = Math.max(0, Math.floor(data.elapsed || 0)); // ✅ Store elapsed time from server

        } catch (error) {
            console.error('Error fetching stopwatch time:', error);
        }
    }

    function updateStopwatchDisplay() {
        const minutes = Math.floor(elapsedSeconds / 60);
        const seconds = elapsedSeconds % 60;
        
        document.getElementById('stopwatch').textContent =
            (minutes < 10 ? "0" + minutes : minutes) + ":" +
            (seconds < 10 ? "0" + seconds : seconds);
    }

    // ✅ Fetch time from the server once when the page loads
    fetchStopwatchTime().then(() => {
        updateStopwatchDisplay(); // Update the UI immediately
        setInterval(() => {
            elapsedSeconds++; // Increment every second
            updateStopwatchDisplay();
        }, 1000);
    });



    // ✅ AJAX-based Check Button
    document.getElementById('sudokuForm').addEventListener('submit', function (event) {
        event.preventDefault(); // Stop form from submitting normally

        let formData = new FormData(this);
        
        fetch('{{ route("check") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json()) // Get JSON response
        .then(data => {
            let resultBox = document.getElementById('gameResult');
            resultBox.textContent = data.message;

            // ✅ Update message styling based on status
            resultBox.classList.remove("d-none", "alert-success", "alert-danger", "alert-warning");
            if (data.status === "Correct") {
                resultBox.classList.add("alert-success");
            } else if (data.status === "SomeIncorrect") {
                resultBox.classList.add("alert-danger");
            } else if (data.status === "GameNotOver") {
                resultBox.classList.add("alert-warning");
            }
        })
        .catch(error => console.error('Error:', error));
    });

    // ✅ AJAX-based Solve Button (Updates Board)
    document.getElementById('solveButton').addEventListener('click', function () {
    fetch('{{ route("solve") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json()) // Get JSON response
    .then(data => {
        let resultBox = document.getElementById('gameResult');
        resultBox.textContent = data.message;
        resultBox.classList.remove("d-none", "alert-danger", "alert-warning");
        resultBox.classList.add("alert-success");

        // ✅ Update the board visually with the solved numbers
        let board = data.board;
        for (let i = 0; i < 9; i++) {
            for (let j = 0; j < 9; j++) {
                let cell = document.querySelector(`input[name="cell_${i}_${j}"]`);
                if (cell && cell.value == "") { // Only fill empty cells
                    cell.value = board[i][j];
                    cell.classList.add("solved"); // Optional styling
                }
            }
        }
    })
    .catch(error => console.error('Error:', error));
});

</script>
@endsection
