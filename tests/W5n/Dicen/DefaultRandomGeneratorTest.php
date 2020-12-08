<?php

namespace Tests\W5n\Dicen;

use PHPUnit\Framework\TestCase;
use W5n\Dicen\DefaultRandomGenerator;

class DefaultRandomGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $generator = new DefaultRandomGenerator();

        for ($i = 0; $i < 100; $i++) {
            $this->assertEquals($i, $generator->generate($i, $i));
        }

        $this->assertTrue($generator->generate(1, 10) >= 1);
        $this->assertTrue($generator->generate(1, 10) <= 10);
    }
}
