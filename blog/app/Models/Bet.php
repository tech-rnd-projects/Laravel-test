<?php 

namespace App\Models;

use \DateTime;

class Bet
{
  protected $amount;

  public function __construct(array $config = [])
  {
    if (isset($config['amount'])) {
      $this->amount = $config['amount'];
    } else {
      $this->amount = 1;
    }
  }

  public function getMultiplier() {
    return $this->amount;
  }
}