<?php

namespace Tests\W5n\Samples;

use PHPUnit\Framework\TestCase;
use W5n\Dicen\DiceRoll;

class DiceRollTest extends TestCase
{
    public function testToString()
    {
        $diceRoll  = new DiceRoll(1, 6, 2);
        $diceRoll2 = new DiceRoll(5, 20, -8);
        $diceRoll3 = new DiceRoll(5, 20, -8, 'With Label');
        $this->assertEquals('1d6+2', strval($diceRoll));
        $this->assertEquals('5d20-8', strval($diceRoll2));
        $this->assertEquals('5d20-8[With Label]', strval($diceRoll3));
    }

    public function testDiceInfo()
    {
        $diceRoll  = new DiceRoll(1, 6, 2, 'TestDice');
        $this->assertEquals(1, $diceRoll->getDiceCount());
        $this->assertEquals(6, $diceRoll->getDiceFaces());
        $this->assertEquals(2, $diceRoll->getModifier());
        $this->assertEquals('TestDice', $diceRoll->getLabel());
    }

    public function testDiceCannotHave0Faces()
    {
        $this->expectException(\Exception::class);
        $diceRoll  = new DiceRoll(1, 0, 2, 'TestDice');
    }
}
