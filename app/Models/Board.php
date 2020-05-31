<?php 

namespace App\Models;

use \DateTime;
use App\Interfaces\IBoard;
use App\Interfaces\IBet;
use App\Interfaces\IBetResult;
use App\Models\BetResult;
use App\Models\RowResult;
use App\Models\Symbol;
use App\Common\Helpers\SymbolHelper;
use Illuminate\Support\Facades\Log;

class Board implements IBoard
{
  protected $rows;
  protected $cols;
  protected $board;
  protected $boardValues;

  public function __construct(array $config = [])
  {
    if (isset($config['rows'])) {
      $this->rows = $config['rows'];
    }
    if (isset($config['cols'])) {
      $this->cols = $config['cols'];
    }
    if (isset($config['boardValues'])) {
      $this->boardValues = $config['boardValues'];
    }

    $this->generateBoard();
  }

  /**
   * get results for every payline in the bet. includes all row matches with every payline, even empty matches.
   * @return IBetResult result of each payline.
   */
  public function placeBet(IBet $bet) : IBetResult {
    Log::debug("[placeBet] \n");
    $payOut = 0;
    $paylinesMatches = []; // results of each payline against every row.
    $paylines = $bet->getPaylines();
    for ($i = 0; $i < count($paylines); $i++) {
      $payline = $paylines[$i];
      $rowMatches = $this->paylineRowResults($payline);
      $paylineResult = new PaylineResult($payline, $rowMatches);
      array_push($paylinesMatches, $paylineResult);
    }

    $result = new BetResult($bet, $this, $paylinesMatches);

    return $result;
  }

  public function values() : array {
    $values = [];
    $board = $this->board;
    for ($rowIndex = 0; $rowIndex < $this->rows; $rowIndex++) {
      $row = $board[$rowIndex];
      for ($colIndex = 0; $colIndex < $this->cols; $colIndex++) {
        $cell = $row[$colIndex];
        $slug = $cell->getSlug();
        array_push($values, $slug);
      }
    }

    return $values;
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

  /**
   * check payline for any matches in the board rows. and return rows with any symbols sequence.
   *
   * @param string $payline payline as in symbols seperated by empty spaces.
   * @param int $minMatch minimun tolerence for consecutive symbols match in the row.
   * @return array all paylines results and any matched 'row' from the board, and the 'sequence' matching from payline with the row.
  */
  private function paylineRowResults(string $payline, int $minMatch = 3) : array {
    Log::debug("\n");
    Log::debug("[paylineRowResults] payline:". $payline);
    $paylineValues = explode(' ', $payline);
    $paylineSum = count($paylineValues);
    $rowMatches = []; // track every row matches against the payline. 1 payline has many row match
    $foundSymbols = []; // found symbols in the current row search
    $board = $this->board;
    // loop each symbol in payline, init pointers to 0.
    // if match symbol set rowIndex=previouseRowIndex & colIndex=previouseColIndex
    // if noMatch go to next row rowIndex = rowIndex++ & colsIndex = 0
    $pIndex = 0;
    $rowIndex = 0;
    $colIndex = 0;
    $foundMatch = false; // if there a match, exit board loops and keep pointers track to go next cell in row-col
    // keep track of the previouse last matched index so it will be consecutive.
    $previouseRowIndex = -1;
    $previouseCollIndex = -1;
    for ($pIndex; $pIndex < $paylineSum; $pIndex++) {
      if ($foundMatch) {
        // searching ne
      } else {
        // reset for next symbol search
        $foundSymbols = [];
        // $rowIndex = 0;
        // $colIndex = 0;
      }
      $pValue = $paylineValues[$pIndex]; // payline value to check match against cell value
      // to make sure that row Index and Column Index are keeping track of pointer position for matched cases.
      Log::debug("p:" . $pIndex . " r:" . $rowIndex . " c:" . $colIndex);
      // avoid out of bounds rows
      if ($rowIndex < $this->rows) {
        // check board row by column
        for ($rowIndex; $rowIndex < $this->rows; $rowIndex++) {
          // every symbol-cell match must reset.
          $foundMatch = false;
          $row = $board[$rowIndex];
          // debug
          $toRPrint = array_map(function ($rr) { return $rr->getValue();}, $row);
          Log::debug("[start] row:" . $rowIndex . " val:[" . implode(" ", $toRPrint) . "] col:" . $colIndex);
          if ($colIndex < $this->cols) {
            for ($colIndex; $colIndex < $this->cols; $colIndex++) {
              $cell = $row[$colIndex];
              $cellSymbol = $cell->getSlug();
              $cellVal = $cell->getValue();

              // found match
              if ($pValue == $cellVal) {
                Log::debug("[found] " . $pValue . "= cellSymbol: " . $cellSymbol);
                $foundMatch = true;
              }
              if ($foundMatch) {
                $previouseCollIndex = $colIndex;
                $previouseRowIndex = $rowIndex;
                array_push($foundSymbols, $cell);
                // move pointer to next cell
                $colIndex++;
                // debug found consecutive symbols so far
                $toPrint = array_map(function ($c) { return $c->getSlug();}, $foundSymbols);
                Log::debug("consecutive-matches: found symbols[" . implode(", ", $toPrint) . "]");
                break; // go to next symbol in payline, and keep pointers in board
              } else {
                $previouseCollIndex = -1;
                $previouseRowIndex = -1;
                // if found < 3 then reset foundSymbols to empty.
                if (count($foundSymbols) < $minMatch) {
                  Log::debug("[clear] cellVal: " . $cellVal);
                  // clear, dont bother if its less then minMatch
                  $foundSymbols = [];
                }
              }

            } // end column search
          }
          // stay on same raw if colIndex is not last cell in row
          if ($foundMatch && $colIndex < $this->cols) {
            Log::debug("break: rows col:" . $colIndex);
            break; // go to next symbol in payline, and keep pointers in board
          }
          // end of row always store result of any match
          // 1 payline might have multiple matches with multiple rows, so keep track for every row matched and not.
          // later calculate which is the biggest sequence match to get total winning.
          $rowResult = new RowResult($foundSymbols, $row);
          array_push($rowMatches, $rowResult);
          $colIndex = 0; // start next row and first column
          Log::debug("[end] row: " . $rowIndex . " count:" . count($rowMatches));
        } // end row search
      }
    }

    return $rowMatches;
  }

  private function buildStaticBoard() : void {
    $board = [];
    $symbols = explode(' ', $this->boardValues);

    // must have equal or more symbols then the board can have. if more, the extra are ignored.
    if (($this->rows * $this->cols) == count($symbols)) {
      // build as row by column
      $cellIndex = 0;
      for ($rowIndex = 0; $rowIndex < $this->rows; $rowIndex++) {
        $row = [];
        for ($colIndex = 0; $colIndex < $this->cols; $colIndex++) {
          $nextSymbol = $symbols[$cellIndex];
          $slug = SymbolHelper::findSymbol($nextSymbol);
          $value = $rowIndex + ($colIndex * $this->rows);
          $cell = new Symbol($cellIndex, $slug, $value);

          // Log::debug("[buildBoard] :". $cellIndex . ", " . $slug . ", " . $value ."\n");
          $row[$colIndex] = $cell;
          $cellIndex++;
        }

        $board[$rowIndex] = $row;
      }

      $this->board = $board;
    }
  }

  private function buildBoard() : void {
    $board = [];

    // build as row by column
    $cellIndex = 0;
    for ($rowIndex = 0; $rowIndex < $this->rows; $rowIndex++) {
      $row = [];
      for ($colIndex = 0; $colIndex < $this->cols; $colIndex++) {
        $slug = SymbolHelper::random();
        $value = $rowIndex + ($colIndex * $this->rows);
        $cell = new Symbol($cellIndex, $slug, $value);

        // Log::debug("[buildBoard] :". $cellIndex . ", " . $slug . ", " . $value ."\n");
        $row[$colIndex] = $cell;
        $cellIndex++;
      }

      $board[$rowIndex] = $row;
    }

    $this->board = $board;
  }

  private function generateBoard() : void {
    if ($this->boardValues) {
      $this->buildStaticBoard();
    } else {
      $this->buildBoard();
    }
  }
}