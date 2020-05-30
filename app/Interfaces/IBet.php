<?php

namespace App\Interfaces;

interface IBet
{
  public function getAmount() : int;
  public function getPaylines() : string;
}