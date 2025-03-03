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
        $sudoku->Board = $generator->Given; // Initialize board with given numbers
        $sudoku->Solved = $generator->Solved; // Store the solution
        $sudoku->Difficulty = $chosenDifficulty;

        // Store the puzzle in session
        Session::put('SudokuPuzzle', serialize($sudoku));
        Session::put('GameStartTime', Carbon::now()->toIso8601String());
        Session::forget('StopTime');

        return view('sudoku', compact('sudoku'));
    }

    // POST: /sudoku/check
    public function check(Request $request)
    {
        $sudoku = unserialize(Session::get('SudokuPuzzle'));
        if (!$sudoku) {
            return redirect()->route('home');
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

        if ($status == "GameNotOver") {
            $message = "The game isn't over yet.";
            Session::forget('StopTime');
        } elseif ($status == "SomeIncorrect") {
            $message = "Some entries are incorrect.";
            Session::forget('StopTime');
        } else {
            $message = "Congratulations! You solved the puzzle.";
            Session::put('StopTime', Carbon::now()->toIso8601String());
        }

        // Store updated game state
        Session::put('SudokuPuzzle', serialize($sudoku));

        return view('sudoku', compact('sudoku', 'message'));
    }

    // POST: /sudoku/solve
    public function solve()
    {
        $sudokuGame = unserialize(Session::get('SudokuPuzzle'));
        if (!$sudokuGame) {
            return redirect()->route('home');
        }

        // ✅ Fill in missing values with the solution (don't generate a new puzzle)
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                if ($sudokuGame->Board[$i][$j] == 0) {
                    $sudokuGame->Board[$i][$j] = $sudokuGame->Solved[$i][$j];
                }
            }
        }

        Session::put('SudokuPuzzle', serialize($sudokuGame));

        return view('sudoku', ['sudoku' => $sudokuGame, 'message' => "The puzzle has been solved."]);
    }

    // Helper function to check puzzle status
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
