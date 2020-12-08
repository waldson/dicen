<?php

namespace Tests\W5n\Samples;

use PHPUnit\Framework\TestCase;
use W5n\Dicen\Operation;
use W5n\Dicen\Operator;
use W5n\Dicen\Number;

class OperationTest extends TestCase
{
    public function testInvalidOperator()
    {
        $this->expectException(\Exception::class);
        $operation = new Operation(
            new Operator('#', 1, false),
            new Number(1),
            new Number(2)
        );

        $operation->getValue();
    }
}
