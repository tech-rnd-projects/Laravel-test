<?php namespace App\Common\Helpers;

class SymbolHelper {
  public static function getSymbols() : array {
    $symbolMapping = [
      0 => 'A',
      1 => '9',
      2 => '10',
      3 => 'J',
      4 => 'Q',
      5 => 'K',
      6 => 'cat',
      7 => 'dog',
      8 => 'monkey',
      9 => 'bird',
    ];

    return $symbolMapping;
  }

  
  public static function random() : string {
    $symbols = SymbolHelper::getSymbols();
    $countSymbols = count($symbols);
    $index = rand(0, $countSymbols);
    $value = $symbols[$index];

    return $value;
  }
}