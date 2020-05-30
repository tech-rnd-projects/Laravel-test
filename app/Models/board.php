<?php 

namespace App\Models;

use \DateTime;
use App\Models\Bet;
use App\Models\Symbol;
use App\Common\Helpers\SymbolHelper;

class Board
{
  protected $rows;
  protected $cols;
  protected $board;

  public function __construct(array $config = [])
  {
    if (isset($config['rows'])) {
      $this->rows = $config['rows'];
    }
    if (isset($config['cols'])) {
      $this->cols = $config['cols'];
    }

    $this->generateBoard();
  }

  public function generateBoard() : array {
    $this->buildBoard();

    return $this->board;
  }

  public function play($payline) : void {

  }

  public function print() : string {
    $output = '';

    $board = $this->board;
    for ($rowIndex = 0; $rowIndex < $this->rows; $rowIndex++) {
      $output .= "line: ";
      $row = $board[$rowIndex];
      for ($colIndex = 0; $colIndex < $this->cols; $colIndex++) {
        $cell = $row[$colIndex];
        $value = $cell->getValue();
        $slug = $cell->getSlug();
        $id = $cell->getId();
        $output .= "\t[id:" . $id . " s:".$slug." val:".$value."]";
      }
      $output .= "\n\r";
    }

    return $output;
  }

  private function buildBoard() {
    $board = [];

    // build as row by column
    $cellIndex = 0;
    for ($rowIndex = 0; $rowIndex < $this->rows; $rowIndex++) {
      $row = [];
      for ($colIndex = 0; $colIndex < $this->cols; $colIndex++) {
        $slug = SymbolHelper::random();
        $value = $rowIndex + ($colIndex * $this->rows);
        $cell = new Symbol($cellIndex, $slug, $value);

        $row[$colIndex] = $cell;
        $cellIndex++;
      }

      $board[$rowIndex] = $row;
    }

    $this->board = $board;
  }

}