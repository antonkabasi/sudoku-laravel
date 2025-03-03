<?php

namespace App\Models;

class SudokuGame
{
    public $Board;
    public $Difficulty;

    public function __construct()
    {
        $this->clearBoard();
    }

    public function clearBoard()
    {
        $this->Board = [];
        for ($i = 0; $i < 9; $i++) {
            $this->Board[$i] = array_fill(0, 9, 0);
        }
    }

    public function generatePuzzle(string $difficulty)
    {
        $this->Difficulty = $difficulty;
        $this->clearBoard();

        switch (strtolower($difficulty)) {
            case "easy":
                $cellsToFill = 40;
                break;
            case "medium":
                $cellsToFill = 30;
                break;
            case "hard":
                $cellsToFill = 20;
                break;
            default:
                $cellsToFill = 30;
                break;
        }

        for ($n = 0; $n < $cellsToFill; $n++) {
            $i = rand(0, 8);
            $j = rand(0, 8);
            $this->Board[$i][$j] = rand(1, 9);
        }
    }
}
