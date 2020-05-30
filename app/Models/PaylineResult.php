<?php 

namespace App\Models;

use \DateTime;
use App\Interfaces\IPaylineResult;
use App\Interfaces\IRowResult;

class PaylineResult implements IPaylineResult
{
  protected $payline;
  protected $rowMatches; // all the rows results for the given payline, even empty results.

  public function __construct(array $payline, array $rowMatches)
  {
    $this->payline = $payline;
    $this->rowMatches = $rowMatches;
  }

  /**
   * filter the maximun rowMaches by having the highest symbol match, give empty array for no matches.
   * @return array the row with the highest consecutive match of symbols
   */
  public function getMaxRowMatch() : IRowResult {
    $maxMatch = [];
    $symbolsfound = 0;
    for ($r = 0; $r < count($this->rowMatches); $r++) {
      $rowMatch = $this->rowMatches[$r];
      $found = $rowMatch->getFoundSymbols();
      if ($found > $symbolsfound) {
        $maxMatch = $rowMatch;
      }
    }

    return $maxMatch;
  }

  public function getPayline() : array {
    return $this->payline;
  }

  public function getRowMatches() : array {
    return $this->rowMatches;
  }
}