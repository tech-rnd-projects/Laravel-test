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
    Log::info("[placeBet] \n");
    $payOut = 0;
    $paylinesMatches = []; // results of each payline against every row.
    $paylines = $bet->getPaylines();
    for ($i = 0; $i < count($paylines); $i++) {
      $payline = $paylines[$i];
      $rowMatches = $this->paylineRowResults($payline);
      $paylineResult = new PaylineResult($payline, $rowMatches);
      array_push($paylinesMatches, $paylineResult);
    }

    $result = new BetResult($bet, $paylinesMatches);

    return $result;
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
    $symbols = explode(' ', $payline);
    $sum = count($symbols);
    $rowMatches = [];
    $board = $this->board;
    $foundSymbols = [];
    Log::info("[paylineRowResults] payline:". $payline);
    for ($rowIndex = 0; $rowIndex < $this->rows; $rowIndex++) {
      $row = $board[$rowIndex];
      $previouseCellIndex = -1;
      for ($colIndex = 0; $colIndex < $this->cols; $colIndex++) {
        $cell = $row[$colIndex];
        $cellSymbol = $cell->getSlug();
        $cellVal = $cell->getValue();

        Log::info("[paylineRowResults]cellValue:" . $cellVal . " cellSymbol: ". $cellSymbol . " rowIndex:". $rowIndex . " colIndex:". $colIndex);
        for ($s = 0; $s < $sum; $s++) {
          $symbol = $symbols[$s];
          // must be consecutive
          if ($symbol == $cellSymbol && $previouseCellIndex + 1 == $colIndex) {
            Log::info("[paylineRowResults][found] ". $symbol);
            array_push($foundSymbols, $cell);
            $previouseCellIndex = $colIndex;
          } else if (count($foundSymbols) < $minMatch) {
            // Log::info("[paylineRowResults] clear");
            // clear, dont bother if its less then minMatch
            $foundSymbols = [];
          }
        }
      }
      // 1 payline might have multiple matches with multiple rows.
      // later calculate which is the biggest sequence match.
      $rowResult = new RowResult($foundSymbols, $row);
      array_push($rowMatches, $rowResult);
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

          // Log::info("[buildBoard] :". $cellIndex . ", " . $slug . ", " . $value ."\n");
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

        // Log::info("[buildBoard] :". $cellIndex . ", " . $slug . ", " . $value ."\n");
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