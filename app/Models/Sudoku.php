<?php

namespace App\Models;

class Sudoku extends Generator
{
    public $UserGrid; // 9x9 array for the player's grid
    public $Difficulty;

    // If a difficulty is provided, start a new game.
    public function __construct($difficulty = null)
    {
        parent::__construct();
        $this->Initialize();
        if ($difficulty !== null) {
            $this->Difficulty = $difficulty;
            $this->NewGame($difficulty);
        }
    }

    // Initialize the UserGrid.
    private function Initialize()
    {
        $this->UserGrid = [];
        for ($i = 0; $i < 9; $i++) {
            $this->UserGrid[$i] = array_fill(0, 9, 0);
        }
    }

    // Updates the user grid.
    public function UpdateUserGrid($grid)
    {
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                $this->UserGrid[$i][$j] = $grid[$i][$j];
            }
        }
    }

    // Checks if the user grid matches the solved puzzle.
    public function Check()
    {
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                if ($this->UserGrid[$i][$j] == 0 || $this->UserGrid[$i][$j] != $this->Solved[$i][$j]) {
                    return false;
                }
            }
        }
        return true;
    }

    // Returns the game status.
    public function CheckStatus()
    {
        // Check if any cell is empty.
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                if ($this->UserGrid[$i][$j] == 0) {
                    return "GameNotOver";
                }
            }
        }
        // Check for incorrect entries.
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                if ($this->UserGrid[$i][$j] != $this->Solved[$i][$j]) {
                    return "SomeIncorrect";
                }
            }
        }
        return "Correct";
    }
}
