<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use App\Models\SudokuGame;
use App\Models\Generator;

class SudokuController extends Controller
{
    // GET: /sudoku?difficulty=easy|medium|hard
    public function index(Request $request)
    {
        $difficultyParam = $request->query('difficulty', 'medium');
        $chosenDifficulty = ucfirst(strtolower($difficultyParam));

        // Create a new Sudoku puzzle
        $generator = new Generator();
        $generator->NewGame($chosenDifficulty);

        $sudoku = new SudokuGame();
        $sudoku->Board = $generator->Given;
        $sudoku->Solved = $generator->Solved;
        $sudoku->Difficulty = $chosenDifficulty;

        // Store the puzzle in session
        Session::put('SudokuPuzzle', serialize($sudoku));
        Session::put('GameStartTime', Carbon::now()->toIso8601String());
        Session::forget('StopTime'); // Ensure timer resets

        return view('sudoku', compact('sudoku'));
    }

    // POST: /sudoku/check
    public function check(Request $request)
    {
        $sudoku = unserialize(Session::get('SudokuPuzzle'));
        if (!$sudoku) {
            return response()->json(['status' => 'error', 'message' => 'No puzzle found.']);
        }

        // Update board with user inputs
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                $fieldName = "cell_{$i}_{$j}";
                $value = $request->input($fieldName, 0);
                $sudoku->Board[$i][$j] = is_numeric($value) ? (int) $value : 0;
            }
        }

        // Check if the puzzle is solved
        $status = $this->checkStatus($sudoku->Board, $sudoku->Solved);
        $message = "";

        if ($status == "GameNotOver") {
            $message = "🟡 The game isn't over yet.";
            Session::forget('StopTime');
        } elseif ($status == "SomeIncorrect") {
            $message = "❌ Some entries are incorrect.";
            Session::forget('StopTime');
        } else {
            $message = "🎉 Congratulations! You solved the puzzle.";
            if (!Session::has('StopTime')) {
                Session::put('StopTime', Carbon::now()->toIso8601String());
            }
        }

        // Store updated game state
        Session::put('SudokuPuzzle', serialize($sudoku));

        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    // POST: /sudoku/solve
    public function solve()
    {
        $sudokuGame = unserialize(Session::get('SudokuPuzzle'));

        if (!$sudokuGame) {
            return response()->json(['status' => 'error', 'message' => 'No puzzle found.']);
        }

        // ✅ Overwrite all incorrect values and empty cells with the correct solution
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                if ($sudokuGame->Board[$i][$j] != $sudokuGame->Solved[$i][$j]) { 
                    // Replace incorrect values and empty cells with the correct solution
                    $sudokuGame->Board[$i][$j] = $sudokuGame->Solved[$i][$j];
                }
            }
        }

        // Store the updated board in session
        Session::put('SudokuPuzzle', serialize($sudokuGame));

        return response()->json([
            'status' => 'solved',
            'message' => '✅ The puzzle has been corrected.',
            'board' => $sudokuGame->Board
        ]);
    }


    public function stopwatchTime()
    {
        $startTimeStr = Session::get('GameStartTime');

        if (!$startTimeStr) {
            return response()->json(['elapsed' => 0]);
        }

        try {
            $startTime = Carbon::parse($startTimeStr);
            $stopTimeStr = Session::get('StopTime');
            $effectiveTime = $stopTimeStr ? Carbon::parse($stopTimeStr) : Carbon::now();

            $elapsed = max(0, $effectiveTime->diffInSeconds($startTime));

            return response()->json([
                'elapsed' => $elapsed,
                'stopped' => $stopTimeStr ? true : false
            ]);

        } catch (\Exception $e) {
            return response()->json(['elapsed' => 0, 'error' => 'Invalid timestamp'], 500);
        }
    }

    private function checkStatus($userGrid, $solvedGrid)
    {
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                if ($userGrid[$i][$j] == 0) {
                    return "GameNotOver";
                }
            }
        }

        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                if ($userGrid[$i][$j] != $solvedGrid[$i][$j]) {
                    return "SomeIncorrect";
                }
            }
        }
        return "Correct";
    }
}
