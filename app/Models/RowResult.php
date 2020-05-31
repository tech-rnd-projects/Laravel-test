<?php 

namespace App\Models;

use \DateTime;
use App\Interfaces\IRowResult;

class RowResult implements IRowResult
{
  protected $foundSymbols; // number of symbols that are in consecutive match against board rows
  protected $row;

  public function __construct(array $foundSymbols, array $row)
  {
    $this->foundSymbols = $foundSymbols;
    $this->row = $row;
  }

  public function getFoundSymbols() : array {
    return $this->foundSymbols;
  }

  public function getRow() : array {
    return $this->row;
  }

}