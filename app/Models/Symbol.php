<?php 

namespace App\Models;

class Symbol
{
  protected $slug;
  protected $value;

  public function __construct(string $slug, string $value)
  {
    $this->slug = $slug;
    $this->value = $value;
  }

  public function getSlug() : string {
    return $this->slug;
  }

  public function getValue() : string {
    return $this->value;
  }
}