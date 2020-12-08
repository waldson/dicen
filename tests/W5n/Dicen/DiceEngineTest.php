<?php

namespace Tests\W5n\Dicen;

use PHPUnit\Framework\TestCase;
use W5n\Dicen\DefaultRandomGenerator;
use W5n\Dicen\DiceEngine;
use W5n\Dicen\DiceParser;
use Mockery;

class DiceEngineTest extends TestCase
{
    public function testRoll()
    {
        $gen = Mockery::mock(DefaultRandomGenerator::class);
        $gen->shouldReceive('generate')
            ->times(10)
            ->andReturn(1);

        $engine = new DiceEngine(
            new DiceParser(),
            $gen
        );

        $result = $engine->roll('4d6-4[Fire]+1d6[Ice]');

        $this->assertEquals(1, $result);
    }

    public function testPureMath()
    {
        $engine = new DiceEngine(
            new DiceParser(),
            new DefaultRandomGenerator()
        );

        $this->assertEquals(10, $engine->roll('6+4'));
        $this->assertEquals(10, $engine->roll('5*2'));
        $this->assertEquals(10, $engine->roll('(4+1)*2'));
        $this->assertEquals(10, $engine->roll('(10*2)/2'));
        $this->assertEquals(10, $engine->roll('10*2-10'));
        $this->assertEquals(10, $engine->roll('2+4*2'));
        $this->assertEquals(16, $engine->roll('(2+2)^2'));
        $this->assertEquals(1, $engine->roll('(2+2)%3'));
    }

    public function testMathAndDiceMixed()
    {
        $gen = Mockery::mock(DefaultRandomGenerator::class);
        $gen->shouldReceive('generate')
            ->times(2)
            ->andReturn(10);

        $engine = new DiceEngine(
            new DiceParser(),
            $gen
        );

        $result = $engine->roll('2d100*2+2');
        $result2 = $engine->roll('2d100*(2+2)');
        $result3 = $engine->roll('2d100x(2+2)');

        $this->assertEquals(42, $result);
        $this->assertEquals(80, $result2);
        $this->assertEquals(80, $result3);
    }

    public function testCreateContext()
    {
        $parser = new DiceParser();
        $gen    = new DefaultRandomGenerator();
        $engine = new DiceEngine($parser, $gen);
        $params = ['foo' => 'bar', 'bar' => 'foo'];

        $context = $engine->createContext($params);

        $this->assertEquals($parser, $context->getParser());
        $this->assertEquals($gen, $context->getRandomGenerator());
        $this->assertEquals($engine, $context->getEngine());
        $this->assertEquals('bar', $context->getParam('foo'));
        $this->assertEquals('foo', $context->getParam('bar'));
        $this->assertNull($context->getParam('invalid'));
        $this->assertEquals(10, $context->getParam('invalid', 10));
        $this->assertEquals($params, $context->getParams());
    }
}
