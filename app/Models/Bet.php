<?php 

namespace App\Models;

use \DateTime;
use App\Interfaces\IBet;

class Bet implements IBet
{
  protected $amount;
  protected $paylines;

  public function __construct(int $amount, array $paylines)
  {
    $this->amount = $amount;
    $this->paylines = $paylines;
  }

  public function getAmount() : int {
    return $this->amount;
  }

  public function getPaylines() : array {
    return $this->paylines;
  }
}