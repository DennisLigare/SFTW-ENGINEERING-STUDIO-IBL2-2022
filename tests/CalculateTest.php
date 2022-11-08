<?php

use App\Calculate;
use PHPUnit\Framework\TestCase;

class CalculateTest extends TestCase
{
  public function testAddition()
  {
    $calculator = new Calculate();

    return $this->assertEquals(2, $calculator->add(1, 1));
  }
}
