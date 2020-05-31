<?php

namespace App\Interfaces;

// a single match of payline against 1 single row
interface IRowResult
{
  public function getFoundSymbols() : array;
  public function getRow() : array;
}