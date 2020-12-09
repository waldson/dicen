<?php

namespace Tests\W5n\Dicen;

use PHPUnit\Framework\TestCase;
use W5n\Dicen\DiceDiscard;

class DiceDiscardTest extends TestCase
{
    public function testThrownExceptionWithInvalidType()
    {
        $this->expectException(\Exception::class);
        new DiceDiscard('invalid_type', 0);
    }

    public function testGetCount()
    {
        $discard = new DiceDiscard(DiceDiscard::TYPE_NONE, 10);

        $this->assertEquals(10, $discard->getCount());
    }

    public function testGetType()
    {
        $discard = new DiceDiscard(DiceDiscard::TYPE_NONE, 0);

        $this->assertEquals(DiceDiscard::TYPE_NONE, $discard->getType());
    }
}
