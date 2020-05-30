<?php 

namespace App\Models;

use \DateTime;
use App\Interfaces\IBetResult;

class BetResult implements IBetResult
{
  // $paylinesMatches is an array of paylineResult which has shape of [RowResult, payline]
  protected $paylinesMatches;

  public function __construct(array $paylinesMatches)
  {
    $this->paylinesMatches = $paylinesMatches;
  }

  /**
   * winnnings for each payline
   */
  public function getDetailWinnings() : array {
    // paylines that had more then 3 symbols match. plus get the max row match.
    $matchPaylines = [];
    $betAmount = 100;
    $totalWin = 0;
    for ($i = 0; $i < count($this->paylinesMatches); $i++) {
      $paylineResult = $paylinesMatches[$i];
      $payline = $paylineResult->getPayline();
      $rowMatch = $paylineResult->getMaxRowMatch();
      $foundSymbols = $rowMatch->getFoundSymbols();
      if ($foundSymbols > 3) {
        // print the paylines that had the maximun matches,
        // if no match dont print the payline.
        array_push($matchPaylines, $payline);
        // now cal winnings
        $totalWin += calculateWinnings($betAmount, $foundSymbols);
      }
    }

    $result = [
      'board' => '[J, J, J, Q, K, cat, J, Q, monkey, bird, bird, bird, J, Q, A]',
      'paylines' => implode(',', $matchPaylines),
      'bet_amount' => $betAmount,
      'total_win' => $totalWin,
    ];
    // ToDo -loop in every payline result to remove empty once and calculate biggest match winning
    return $result;
  }

  private function calculateWinnings($betAmount, $foundSymbols) : integer {
    $percentage = 0;
    if ($foundSymbols >= 5) {
      $percentage = 1000;
    } else if ($foundSymbols == 4) {
      $percentage = 200;
    } else if ($foundSymbols == 3) {
      $percentage = 20;
    }

    return $betAmount / $percentage;
  }

}