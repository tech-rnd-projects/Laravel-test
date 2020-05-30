<?php

namespace App\Interfaces;

use App\Interfaces\IRowResult;

// a single match of payline against 1 single row
interface IPaylineResult
{
  public function getMaxRowMatch() : IRowResult;
  public function getPayline() : array;
  public function getRowMatches() : array;
}