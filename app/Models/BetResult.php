<?php 

namespace App\Models;

use \DateTime;
use Illuminate\Support\Facades\Log;
use App\Interfaces\IBoard;
use App\Interfaces\IBet;
use App\Interfaces\IBetResult;

class BetResult implements IBetResult
{
  // $paylinesMatches is an array of paylineResult which has shape of [RowResult, payline]
  protected $paylinesMatches;
  protected $board;
  protected $bet;

  public function __construct(IBet $bet, IBoard $board, array $paylinesMatches)
  {
    $this->paylinesMatches = $paylinesMatches;
    $this->bet = $bet;
    $this->board = $board;
  }

  /**
   * winnnings for each payline
   */
  public function getDetailWinnings() : array {
    // paylines that had more then 3 symbols match. plus get the max row match.
    $matchPaylines = []; // [{'payline as text' : foundSymbols}],
    $betAmount = $this->bet->getAmount();
    $totalWin = 0;
    for ($i = 0; $i < count($this->paylinesMatches); $i++) {
      $paylineResult = $this->paylinesMatches[$i];
      $payline = $paylineResult->getPayline();
      $rowMatch = $paylineResult->getMaxRowMatch();
      $foundSymbols = $rowMatch->getFoundSymbols();
      $symbolsFound = array_map(function ($c) { return $c->getSlug();}, $foundSymbols);
      $countSymbols = count($symbolsFound);
      if ($countSymbols >= 3) {
        // print the paylines that had the maximun matches,
        // if no match dont print the payline.
        $nPaylines = null;
        $nPaylines[$payline] = $countSymbols;
        array_push($matchPaylines, $nPaylines);
        // now cal winnings
        $paylineWin = $this->calculateWinnings($betAmount, $countSymbols);
        $totalWin += $paylineWin;
        Log::debug("[getDetailWinnings] win:" . $paylineWin);
        Log::debug("[getDetailWinnings]". json_encode($payline) ." symbols:" . implode(", ", $symbolsFound));
      }
    }

    $result = [
      'paylines' => $matchPaylines,
      'total_win' => $totalWin,
    ];
    // ToDo -loop in every payline result to remove empty once and calculate biggest match winning
    return $result;
  }

  private function calculateWinnings(int $betAmount, int $foundSymbols) : int {
    $percentage = 0;
    if ($foundSymbols >= 5) {
      $percentage = 1000;
    } else if ($foundSymbols == 4) {
      $percentage = 200;
    } else if ($foundSymbols == 3) {
      $percentage = 20;
    }

    return (int)(($percentage / 100) * $betAmount);
  }

}