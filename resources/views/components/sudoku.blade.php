@php
    // Assuming $model is passed from the controller,
    // and additional ViewBag-like variables are passed as well.
    $title = "Sudoku Game";
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>{{ $title }}</title>
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/css/bootstrap.min.css" />
    <style>
        /* Fixed message container so messages don't shift the layout */
        #messageContainer {
            height: 50px;
            margin-bottom: 10px;
            overflow: hidden;
        }
        #messageContainer .alert {
            margin: 0;
            line-height: 50px;
            font-size: 1.1em;
        }
        #autoMessage {
            height: 30px;
            margin-bottom: 10px;
            overflow: hidden;
            font-weight: bold;
        }
        .highlight, .highlight input {
            background-color: #c8e6c9 !important;
        }
        table {
            border-collapse: collapse;
        }
        .given {
            background-color: #e9ecef !important;
        }
    </style>
</head>
<body>
<div class="container my-5">
    <h1 class="text-center">{{ $title }}</h1>
    <!-- Difficulty Buttons -->
    <div class="d-flex justify-content-center mb-4">
        <a class="btn btn-primary mx-2" href="{{ route('home.index', ['difficulty' => 'easy']) }}">Easy</a>
        <a class="btn btn-primary mx-2" href="{{ route('home.index', ['difficulty' => 'medium']) }}">Normal</a>
        <a class="btn btn-primary mx-2" href="{{ route('home.index', ['difficulty' => 'hard']) }}">Hard</a>
    </div>
    
    <!-- Fixed Message Container -->
    <div id="messageContainer">
        @if(session('message'))
            <div class="alert alert-info text-center">
                {{ session('message') }}
            </div>
        @endif
    </div>
    
    <!-- Auto-check Message Container -->
    <div id="autoMessage" class="text-center"></div>
    
    <!-- Tabs to switch between Puzzle and Leaderboard -->
    <ul class="nav nav-tabs" id="sudokuTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="puzzle-tab" data-bs-toggle="tab" data-bs-target="#puzzle" type="button" role="tab" aria-controls="puzzle" aria-selected="true">Puzzle</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="leaderboard-tab" data-bs-toggle="tab" data-bs-target="#leaderboard" type="button" role="tab" aria-controls="leaderboard" aria-selected="false">Leaderboard</button>
        </li>
    </ul>
    <div class="tab-content" id="sudokuTabsContent">
        <!-- Puzzle Tab -->
        <div class="tab-pane fade show active" id="puzzle" role="tabpanel" aria-labelledby="puzzle-tab">
            <!-- Puzzle Submission Form (if qualifies for leaderboard) -->
            @if(isset($qualifiedForLeaderboard) && $qualifiedForLeaderboard)
                <div class="card mb-3">
                    <div class="card-header">
                        New Top 10! Submit Your Score
                    </div>
                    <div class="card-body">
                        <form action="{{ route('score.submit') }}" method="post">
                            @csrf
                            <div class="form-group">
                                <label>Player Name</label>
                                <input type="text" name="PlayerName" class="form-control" placeholder="Enter your name" required />
                            </div>
                            <input type="hidden" name="Difficulty" value="{{ $difficulty ?? '' }}" />
                            <input type="hidden" name="StopwatchValue" value="{{ $elapsedTime ?? '' }}" />
                            <button type="submit" class="btn btn-primary">Submit Score</button>
                        </form>
                    </div>
                </div>
            @endif
            <!-- Stopwatch Display -->
            <div class="text-center mb-4">
                <strong>Time:</strong> <span id="stopwatch">00:00</span>
            </div>
            <!-- Sudoku Board Form -->
            <form method="post" action="{{ route('home.check') }}" id="sudokuForm">
                @csrf
                <table class="mx-auto">
                    @for ($i = 0; $i < 9; $i++)
                        <tr>
                            @for ($j = 0; $j < 9; $j++)
                                @php
                                    $borderTop    = ($i % 3 == 0) ? "3px solid black" : "1px solid black";
                                    $borderLeft   = ($j % 3 == 0) ? "3px solid black" : "1px solid black";
                                    $borderRight  = ($j == 8) ? "3px solid black" : "1px solid black";
                                    $borderBottom = ($i == 8) ? "3px solid black" : "1px solid black";
                                    $givenValue = $model['given'][$i][$j];
                                    $userValue  = $model['userGrid'][$i][$j];
                                    $isGiven    = $givenValue != 0;
                                    $cellValue  = $isGiven ? $givenValue : ($userValue != 0 ? $userValue : '');
                                @endphp
                                <td data-row="{{ $i }}" data-col="{{ $j }}" style="width:50px; height:50px; border-top:{{ $borderTop }}; border-left:{{ $borderLeft }}; border-right:{{ $borderRight }}; border-bottom:{{ $borderBottom }}; padding:0;">
                                    <input name="cell_{{ $i }}_{{ $j }}" type="text" class="form-control text-center p-0 sudoku-cell {{ $isGiven ? 'given' : '' }}" maxlength="1" 
                                           value="{{ $cellValue }}" style="height: 100%; border: none;" {{ $isGiven ? 'readonly' : '' }} />
                                </td>
                            @endfor
                        </tr>
                    @endfor
                </table>
                <div class="text-center mt-3">
                    <button type="submit" class="btn btn-success">Check</button>
                    <!-- Uncomment if needed:
                    <button type="submit" formaction="{{ route('home.solve') }}" class="btn btn-danger ml-2">Solve</button>
                    -->
                </div>
            </form>
        </div>
        <!-- Leaderboard Tab -->
        <div class="tab-pane fade" id="leaderboard" role="tabpanel" aria-labelledby="leaderboard-tab">
            {{-- You can include your leaderboard component here --}}
            @livewire('leaderboard')
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- JavaScript for highlighting, auto-check, and stopwatch polling -->
<script>
    // Highlighting logic for clicked cell.
    function clearHighlights() {
        document.querySelectorAll("table td").forEach(cell => cell.classList.remove("highlight"));
    }
    function highlightCells(selectedRow, selectedCol) {
        document.querySelectorAll("table td").forEach(cell => {
            const row = parseInt(cell.getAttribute("data-row"));
            const col = parseInt(cell.getAttribute("data-col"));
            if (row === selectedRow ||
                col === selectedCol ||
                (Math.floor(row / 3) === Math.floor(selectedRow / 3) && Math.floor(col / 3) === Math.floor(selectedCol / 3))) {
                cell.classList.add("highlight");
            }
        });
    }
    document.querySelectorAll("table td input.sudoku-cell").forEach(input => {
        input.addEventListener("click", function () {
            const td = this.closest("td");
            const selectedRow = parseInt(td.getAttribute("data-row"));
            const selectedCol = parseInt(td.getAttribute("data-col"));
            clearHighlights();
            highlightCells(selectedRow, selectedCol);
        });
        // On keyup, trigger auto-check.
        input.addEventListener("keyup", function () {
            checkIfComplete();
        });
    });

    // Auto-check: if every cell is filled, send their values via AJAX to the Validate endpoint.
    function checkIfComplete() {
        const inputs = document.querySelectorAll("table td input.sudoku-cell");
        let complete = true;
        let cellData = {};
        inputs.forEach(input => {
            if (input.value.trim() === "") {
                complete = false;
            }
            cellData[input.name] = input.value.trim();
        });
        if (complete) {
            fetch("{{ route('home.validate') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(cellData)
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById("autoMessage").innerText = data.message;
            })
            .catch(error => console.error('Error validating puzzle:', error));
        } else {
            document.getElementById("autoMessage").innerText = "";
        }
    }

    // Stopwatch polling from the server.
    async function updateStopwatch() {
        try {
            const response = await fetch("{{ route('home.stopwatchTime') }}");
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
</body>
</html>
