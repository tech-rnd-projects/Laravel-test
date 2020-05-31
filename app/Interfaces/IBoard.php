<?php

namespace App\Interfaces;

use App\Interfaces\IBet;
use App\Interfaces\IBetResult;

interface IBoard
{
  public function placeBet(IBet $bet) : IBetResult;
  public function print() : string;
  public function values() : array;
}