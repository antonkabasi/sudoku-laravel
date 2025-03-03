<?php

namespace App\Models;

class SudokuGame
{
    public $Board;       // User's game board (track input)
    public $Solved;      // Fully solved Sudoku grid
    public $Given;       // Grid with given clues
    public $UserGrid;    // User's progress grid
    public $Difficulty;  // Difficulty level
    public $StartTime;   // Track game start time

    public function __construct($difficulty = "medium")
    {
        $this->StartTime = now();
        $this->Difficulty = $difficulty;
        $this->clearBoard();
        $this->generatePuzzle($difficulty);
    }

    public function clearBoard()
    {
        $this->Board = [];
        $this->Solved = [];
        $this->Given = [];
        $this->UserGrid = [];

        for ($i = 0; $i < 9; $i++) {
            $this->Board[$i] = array_fill(0, 9, 0);
            $this->Solved[$i] = array_fill(0, 9, 0);
            $this->Given[$i] = array_fill(0, 9, 0);
            $this->UserGrid[$i] = array_fill(0, 9, 0);
        }
    }

    public function generatePuzzle(string $difficulty)
    {
        $this->Difficulty = $difficulty;
        $this->clearBoard();

        // Generate a fully solved Sudoku board
        $this->generateSolvedBoard();

        // Copy solved board to Given (before removing numbers)
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                $this->Given[$i][$j] = $this->Solved[$i][$j];
            }
        }

        // Remove numbers to create a puzzle
        $cellsToRemove = match (strtolower($difficulty)) {
            "easy" => 81 - 40,
            "medium" => 81 - 30,
            "hard" => 81 - 20,
            default => 81 - 30,
        };

        $randomCells = range(0, 80);
        shuffle($randomCells);

        for ($n = 0; $n < $cellsToRemove; $n++) {
            $i = intdiv($randomCells[$n], 9);
            $j = $randomCells[$n] % 9;
            $this->Given[$i][$j] = 0;
        }

        // Copy Given to Board (this is what the user interacts with)
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                $this->Board[$i][$j] = $this->Given[$i][$j];
            }
        }
    }

    private function generateSolvedBoard()
    {
        // Start with an empty board
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                $this->Solved[$i][$j] = 0;
            }
        }

        // Recursive backtracking to generate a valid Sudoku solution
        $this->solveBoard(0, 0);
    }

    private function solveBoard($row, $col)
    {
        if ($row == 9) return true;

        $nextRow = $col == 8 ? $row + 1 : $row;
        $nextCol = ($col + 1) % 9;

        $digits = range(1, 9);
        shuffle($digits);

        foreach ($digits as $num) {
            if ($this->isValidMove($row, $col, $num)) {
                $this->Solved[$row][$col] = $num;
                if ($this->solveBoard($nextRow, $nextCol)) return true;
                $this->Solved[$row][$col] = 0;
            }
        }
        return false;
    }

    private function isValidMove($row, $col, $num)
    {
        for ($i = 0; $i < 9; $i++) {
            if ($this->Solved[$row][$i] == $num || $this->Solved[$i][$col] == $num) {
                return false;
            }
        }

        $startRow = intdiv($row, 3) * 3;
        $startCol = intdiv($col, 3) * 3;

        for ($i = 0; $i < 3; $i++) {
            for ($j = 0; $j < 3; $j++) {
                if ($this->Solved[$startRow + $i][$startCol + $j] == $num) {
                    return false;
                }
            }
        }
        return true;
    }

    public function checkStatus()
    {
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                if ($this->Board[$i][$j] == 0) {
                    return "GameNotOver";
                }
            }
        }
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                if ($this->Board[$i][$j] != $this->Solved[$i][$j]) {
                    return "SomeIncorrect";
                }
            }
        }
        return "Correct";
    }

    public function solve()
    {
        // Fill user's board with the correct solution
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                $this->Board[$i][$j] = $this->Solved[$i][$j];
            }
        }
    }
}
