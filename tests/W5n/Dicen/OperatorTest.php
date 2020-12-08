<?php

namespace Tests\W5n\Samples;

use PHPUnit\Framework\TestCase;
use W5n\Dicen\Operator;

class OperatorTest extends TestCase
{
    private $operators = [];

    protected function setUp(): void
    {
        $this->operators['+'] = new Operator('+', 1, false);
        $this->operators['-'] = new Operator('+', 1, false);
        $this->operators['*'] = new Operator('+', 2, false);
    }

    /** @test */
    public function testCompare()
    {
        $this->assertEquals(
            0,
            $this->operators['+']->compare($this->operators['-'])
        );

        $this->assertEquals(
            1,
            $this->operators['*']->compare($this->operators['-'])
        );

        $this->assertEquals(
            -1,
            $this->operators['-']->compare($this->operators['*'])
        );
    }

    public function testGetValue()
    {
        $this->assertEquals(0, $this->operators['+']->getValue());
        $this->assertEquals(0, $this->operators['-']->getValue());
        $this->assertEquals(0, $this->operators['*']->getValue());
    }
}
