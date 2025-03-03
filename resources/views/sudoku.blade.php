@extends('layouts.app')

@section('title', 'Sudoku Game')

@section('content')
<div class="container text-center">
    <h1 class="text-2xl font-bold mb-4">Sudoku Game</h1>

    <!-- Difficulty Selection -->
    <div class="flex justify-center my-3">
        <a class="btn btn-primary mx-2" href="{{ route('home', ['difficulty' => 'easy']) }}">Easy</a>
        <a class="btn btn-primary mx-2" href="{{ route('home', ['difficulty' => 'medium']) }}">Normal</a>
        <a class="btn btn-primary mx-2" href="{{ route('home', ['difficulty' => 'hard']) }}">Hard</a>
    </div>

    <!-- Fixed message container so messages don't shift layout -->
    <div id="gameResult" class="alert text-center mt-3 h-12 invisible" style="height:50px; margin-bottom:10px; overflow:hidden;"></div>

    <!-- Sudoku Board -->
    <form id="sudokuForm" action="{{ route('check') }}" method="POST">
        @csrf
        <table class="mx-auto border-collapse">
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
                            <input type="text" name="cell_{{ $i }}_{{ $j }}" 
                                   class="form-control text-center p-0 sudoku-cell {{ $givenValue != 0 ? 'given' : '' }}" 
                                   maxlength="1" value="{{ $cellValue }}" 
                                   style="height: 100%; border: none; {{ $givenValue != 0 ? 'background-color: #e9ecef;' : '' }}" 
                                   {{ $givenValue != 0 ? 'readonly' : '' }} />
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

<!-- Custom CSS for Message Container and Highlighting -->
<style>
    /* Fixed message container styling */
    #gameResult {
        /* Height, margin, and overflow are set inline for reserved space */
    }
    /* Highlighting for non-given cells */
    .highlight, .highlight input {
        background-color: #c8e6c9 !important;
    }
    /* Additional styling for originally given cells */
    .given-highlight, .given-highlight input {
        background-color: #c8e6c9 !important; /* Same green highlight */
        border: 2px solid #4caf50; /* For example, a green border to differentiate */
    }
</style>

<!-- JavaScript for AJAX Check & Solve, Timer, and Cell Highlighting -->
<script>
let elapsedSeconds = 0;
let timerInterval = null; // To manage the timer

async function fetchStopwatchTime() {
    try {
        const response = await fetch('{{ route("stopwatch") }}');
        if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
        const data = await response.json();
        elapsedSeconds = Math.max(0, Math.floor(data.elapsed || 0));
        if (data.stopped) {
            stopTimer();
        } else {
            startTimer();
        }
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

// Start the timer
function startTimer() {
    if (timerInterval !== null) return; // Prevent multiple intervals
    timerInterval = setInterval(() => {
        elapsedSeconds++; 
        updateStopwatchDisplay();
    }, 1000);
}

// Stop the timer
function stopTimer() {
    clearInterval(timerInterval);
    timerInterval = null;
}

// Fetch time on page load
fetchStopwatchTime();

// AJAX-based Check Button - stops timer if solved, restarts if not
document.getElementById('sudokuForm').addEventListener('submit', function (event) {
    event.preventDefault();
    let formData = new FormData(this);
    fetch('{{ route("check") }}', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        let resultBox = document.getElementById('gameResult');
        resultBox.textContent = data.message;
        if (data.status === "Correct") {
            stopTimer();
        } else {
            startTimer();
        }
        resultBox.classList.remove("hidden", "invisible", "alert-success", "alert-danger", "alert-warning");
        if (data.status === "Correct") {
            resultBox.classList.add("alert-success");
        } else if (data.status === "SomeIncorrect") {
            resultBox.classList.add("alert-danger");
        } else if (data.status === "GameNotOver") {
            resultBox.classList.add("alert-warning");
        }
        resultBox.style.visibility = 'visible';
    })
    .catch(error => console.error('Error:', error));
});

// AJAX-based Solve Button - solves puzzle (does not stop timer)
document.getElementById('solveButton').addEventListener('click', function () {
    fetch('{{ route("solve") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        let resultBox = document.getElementById('gameResult');
        resultBox.textContent = data.message;
        resultBox.classList.remove("hidden", "invisible", "alert-danger", "alert-warning");
        resultBox.classList.add("alert-success");
        // Update board with correct solution (overwrite all cells)
        let board = data.board;
        for (let i = 0; i < 9; i++) {
            for (let j = 0; j < 9; j++) {
                let cell = document.querySelector(`input[name="cell_${i}_${j}"]`);
                if (cell) {
                    cell.value = board[i][j];
                    cell.classList.add("solved");
                }
            }
        }
    })
    .catch(error => console.error('Error:', error));
});

// ----------------- Cell Highlighting Logic -----------------
function clearHighlights() {
    const cells = document.querySelectorAll("table td");
    cells.forEach(cell => {
        cell.classList.remove("highlight");
        cell.classList.remove("given-highlight");
    });
}

function highlightCells(selectedRow, selectedCol) {
    const cells = document.querySelectorAll("table td");
    cells.forEach(cell => {
        const row = parseInt(cell.getAttribute("data-row"));
        const col = parseInt(cell.getAttribute("data-col"));
        if (
            row === selectedRow ||
            col === selectedCol ||
            (Math.floor(row / 3) === Math.floor(selectedRow / 3) &&
             Math.floor(col / 3) === Math.floor(selectedCol / 3))
        ) {
            // If the cell's input has class 'given', add given-highlight; else add highlight.
            const input = cell.querySelector("input.sudoku-cell");
            if (input && input.classList.contains("given")) {
                cell.classList.add("given-highlight");
            } else {
                cell.classList.add("highlight");
            }
        }
    });
}

// Attach event listeners to all sudoku cell inputs for highlighting
document.querySelectorAll("table td input.sudoku-cell").forEach(input => {
    input.addEventListener("click", function () {
        const td = this.closest("td");
        const selectedRow = parseInt(td.getAttribute("data-row"));
        const selectedCol = parseInt(td.getAttribute("data-col"));
        clearHighlights();
        highlightCells(selectedRow, selectedCol);
    });
    input.addEventListener("keyup", function () {
        // Optionally, trigger auto-check here if desired.
    });
});
</script>
@endsection
