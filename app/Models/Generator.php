<?php

namespace App\Models;

class Generator
{
    public $Solved; // 9x9 array for the solved puzzle
    public $Given;  // 9x9 array for the puzzle with given clues
    private $numberOfEmptyCells;

    public function __construct()
    {
        $this->Solved = [];
        $this->Given  = [];
        for ($i = 0; $i < 9; $i++) {
            $this->Solved[$i] = array_fill(0, 9, 0);
            $this->Given[$i]  = array_fill(0, 9, 0);
        }
    }

    // Starts a new game based on difficulty.
    public function NewGame($difficulty)
    {
        // Generate a solved sudoku matrix.
        $this->Generate();

        // Create an array of indices 0..80 and shuffle them.
        $randomIndices = range(0, 80);
        shuffle($randomIndices);

        // Set difficulty by number of revealed digits.
        switch ($difficulty) {
            case "Easy":
                $this->numberOfEmptyCells = 81 - 45;
                break;
            case "Medium":
                $this->numberOfEmptyCells = 81 - 30;
                break;
            case "Hard":
                $this->numberOfEmptyCells = 81 - 20;
                break;
            default:
                $this->numberOfEmptyCells = 81 - 30;
                break;
        }

        // Copy solved puzzle into Given.
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                $this->Given[$i][$j] = $this->Solved[$i][$j];
            }
        }

        // Remove numbers from the Given puzzle.
        for ($i = 0; $i < $this->numberOfEmptyCells; $i++) {
            $index = $randomIndices[$i];
            $row = intdiv($index, 9);
            $col = $index % 9;
            $this->Given[$row][$col] = 0;
        }
    }

    // Generates a solved sudoku puzzle.
    private function Generate()
    {
        // Prepare a list of digits.
        $digits = range(1, 9);
        // Randomize the first row.
        shuffle($digits);
        for ($i = 0; $i < 9; $i++) {
            $this->Solved[0][$i] = $digits[$i];
        }

        // For the next 3x3 block, remove the first three digits.
        $blockDigits = $digits;
        array_splice($blockDigits, 0, 3);
        shuffle($blockDigits);
        // Fill positions for the next block (rows 1-3, first 3 columns).
        for ($i = 0; $i < 6; $i++) {
            $row = intdiv($i, 3) + 1;
            $col = $i % 3;
            $this->Solved[$row][$col] = $blockDigits[$i];
        }

        // Fill the rest of the grid using recursive backtracking.
        return $this->Solve($this->Solved, 0, 0);
    }

    // Recursive backtracking solver.
    private function Solve(&$solvedMatrix, $row, $col)
    {
        if ($row === 9) {
            return true;
        }

        // If cell is pre-filled, move to the next cell.
        if ($solvedMatrix[$row][$col] != 0) {
            return ($col === 8)
                ? $this->Solve($solvedMatrix, $row + 1, 0)
                : $this->Solve($solvedMatrix, $row, $col + 1);
        }

        $possibleDigits = $this->Possible($solvedMatrix, $row, $col);
        foreach ($possibleDigits as $digit) {
            $solvedMatrix[$row][$col] = $digit;
            if ($col === 8) {
                if ($this->Solve($solvedMatrix, $row + 1, 0)) {
                    return true;
                }
            } else {
                if ($this->Solve($solvedMatrix, $row, $col + 1)) {
                    return true;
                }
            }
        }
        $solvedMatrix[$row][$col] = 0;
        return false;
    }

    // Returns an array of possible digits for the cell.
    private function Possible($solvedMatrix, $row, $col)
    {
        $possible = range(1, 9);

        // Eliminate digits already in the same row and column.
        for ($i = 0; $i < 9; $i++) {
            if ($i !== $col && $solvedMatrix[$row][$i] > 0) {
                $possible = array_diff($possible, [$solvedMatrix[$row][$i]]);
            }
            if ($i !== $row && $solvedMatrix[$i][$col] > 0) {
                $possible = array_diff($possible, [$solvedMatrix[$i][$col]]);
            }
        }
        // Eliminate digits from the same 3x3 block.
        $startRow = intdiv($row, 3) * 3;
        $startCol = intdiv($col, 3) * 3;
        for ($i = $startRow; $i < $startRow + 3; $i++) {
            for ($j = $startCol; $j < $startCol + 3; $j++) {
                if (($i === $row && $j === $col)) continue;
                if ($solvedMatrix[$i][$j] > 0) {
                    $possible = array_diff($possible, [$solvedMatrix[$i][$j]]);
                }
            }
        }
        return array_values($possible);
    }
}
