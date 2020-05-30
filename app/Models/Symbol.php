<?php 
namespace App\Models;

class Symbol
{
  protected $slug;
  protected $value;
  protected $id;

  public function __construct(int $id, string $slug, int $value)
  {
    $this->id = $id;
    $this->slug = $slug;
    $this->value = $value;
  }

  public function getId() : int {
    return $this->id;
  }

  public function getSlug() : string {
    return $this->slug;
  }

  public function getValue() : int {
    return $this->value;
  }
}