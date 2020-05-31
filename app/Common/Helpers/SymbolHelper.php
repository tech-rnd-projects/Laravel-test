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
    $countSymbols = count($symbols) - 1;
    $index = rand(0, $countSymbols);
    $value = $symbols[$index];

    return $value;
  }

  public static function findSymbol(string $text): string {
    $symbols = SymbolHelper::getSymbols();
    $countSymbols = count($symbols);
    $value = '';
    $keys = array_keys($symbols);
    for ($i = 0; $i < $countSymbols; $i++) {
      $symbolVal = $symbols[$keys[$i]];
      if ($symbolVal == $text) {
        $value = $symbolVal;
        break;
      }
    }

    return $value;
  }
}