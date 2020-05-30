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

  public function generateBoard() : void {
    $board = [];

    for ($rowIndex = 0; $rowIndex < $this->rows; $rowIndex++) {
      $row = [];
      for ($colIndex = 0; $colIndex < $this->cols; $colIndex++) {
        $slug = SymbolHelper::random();
        $value = '1';
        $cell = new Symbol($slug, $value);

        $row[$colIndex] = $cell;
      }

      $board[$rowIndex] = $row;
    }

    $this->board = $board;
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
        $output .= "\t[slug:".$slug." val:".$value."]";
      }
      $output .= "\n\r";
    }

    return $output;
  }
}