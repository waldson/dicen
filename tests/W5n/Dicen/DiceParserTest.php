<?php

namespace Tests\W5n\Samples;

use PHPUnit\Framework\TestCase;
use W5n\Dicen\DiceParser;
use W5n\Dicen\Number;
use W5n\Dicen\DiceDiscard;
use W5n\Dicen\Operation;
use W5n\Dicen\DiceRoll;

class DiceParserTest extends TestCase
{
    private $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new DiceParser();
    }

    public function testParseNumber()
    {
        $number = $this->parser->parse('10');
        $this->assertInstanceOf(Number::class, $number);
        $this->assertEquals(10, $number->getValue());
    }

    public function testParseDiceRoll()
    {
        $roll = $this->parser->parse('10d6+3[Fire]');
        $this->assertInstanceOf(DiceRoll::class, $roll);
        $this->assertEquals(10, $roll->getDiceCount());
        $this->assertEquals(6, $roll->getDiceFaces());
        $this->assertEquals(3, $roll->getModifier());
        $this->assertEquals('Fire', $roll->getLabel());
    }

    public function testOperators()
    {
        $op = $this->parser->parse('10+3');
        $this->assertInstanceOf(Operation::class, $op);
        $this->assertInstanceOf(Number::class, $op->getLeft());
        $this->assertInstanceOf(Number::class, $op->getRight());
        $this->assertEquals('+', $op->getOperator()->getSymbol());
        $this->assertEquals(10, $op->getLeft()->getValue());
        $this->assertEquals(3, $op->getRight()->getValue());
        $this->assertFalse($op->getOperator()->isRightAssociative());
    }


    public function testMixedOperators()
    {
        $op = $this->parser->parse('10-3d5');
        $this->assertInstanceOf(Operation::class, $op);
        $this->assertInstanceOf(Number::class, $op->getLeft());
        $this->assertInstanceOf(DiceRoll::class, $op->getRight());
        $this->assertEquals('-', $op->getOperator()->getSymbol());
        $this->assertEquals(10, $op->getLeft()->getValue());

        $roll = $op->getRight();

        $this->assertEquals(3, $roll->getDiceCount());
        $this->assertEquals(5, $roll->getDiceFaces());
        $this->assertEquals(0, $roll->getModifier());
        $this->assertNull($roll->getLabel());
    }

    public function testDiceFollowedByAParenthesisExpression()
    {
        try {
            $op = $this->parser->parse('2d6+(25-33)*2+3d5*4');
            $this->assertNotNull($op);
        } catch (\Exception $ex) {
            $this->fail('A parenthesis expression can appears after a dice roll.');
        }
    }

    public function testDiceWithDiscardKeepLowest()
    {
        $op = $this->parser->parse('2d6kl1');

        $discard = $op->getDiscard();

        $this->assertNotNull($discard);

        $this->assertEquals(DiceDiscard::TYPE_KEEP_LOWEST, $discard->getType());
        $this->assertEquals(1, $discard->getCount());
    }

    public function testDiceWithDiscardKeepHighest()
    {
        $op  = $this->parser->parse('2d6kh1+3');
        $op2 = $this->parser->parse('3d6k2');

        $discard  = $op->getDiscard();
        $discard2 = $op2->getDiscard();

        $this->assertNotNull($discard);
        $this->assertNotNull($discard2);

        $this->assertEquals(DiceDiscard::TYPE_KEEP_HIGHEST, $discard->getType());
        $this->assertEquals(DiceDiscard::TYPE_KEEP_HIGHEST, $discard2->getType());
        $this->assertEquals(1, $discard->getCount());
        $this->assertEquals(2, $discard2->getCount());
    }

    public function testDiceWithDiscardDropHighest()
    {
        $op = $this->parser->parse('2d6dh1');

        $discard = $op->getDiscard();

        $this->assertNotNull($discard);

        $this->assertEquals(DiceDiscard::TYPE_DROP_HIGHEST, $discard->getType());
        $this->assertEquals(1, $discard->getCount());
    }

    public function testDiceWithDiscardDropLowest()
    {
        $op  = $this->parser->parse('2d6dl1+3');
        $op2 = $this->parser->parse('3d6d2');

        $discard  = $op->getDiscard();
        $discard2 = $op2->getDiscard();

        $this->assertNotNull($discard);
        $this->assertNotNull($discard2);

        $this->assertEquals(DiceDiscard::TYPE_DROP_LOWEST, $discard->getType());
        $this->assertEquals(DiceDiscard::TYPE_DROP_LOWEST, $discard2->getType());
        $this->assertEquals(1, $discard->getCount());
        $this->assertEquals(2, $discard2->getCount());
    }

    public function testMismatchParenthesisShouldThrow()
    {
        $this->expectException(\Exception::class);
        $this->parser->parse('(10%2');
    }

    public function testThrowsAtInvalidSymbol()
    {
        $this->expectException(\Exception::class);
        $this->parser->parse('#');
    }

    public function testThrowsAtInvalidNumber()
    {
        $this->expectException(\Exception::class);
        $this->parser->parse('2da');
    }

    public function testThrowsAtDuplicateD()
    {
        $this->expectException(\Exception::class);
        $this->parser->parse('2dd+10');
    }

    public function testThrowsAtIncompleteExpression()
    {
        $this->expectException(\Exception::class);
        $this->parser->parse('2d5+');
    }

    public function testThrowsWithoutOpenParens()
    {
        $this->expectException(\Exception::class);
        $this->parser->parse('2d5+)');
    }

    public function testThrowsWithOnlyCloseParens()
    {
        $this->expectException(\Exception::class);
        $this->parser->parse(')');
    }

    public function testDicesDontJoin()
    {
        $result = $this->parser->parse('2d5+2d5');

        $this->assertInstanceOf(Operation::class, $result);
        $this->assertInstanceOf(DiceRoll::class, $result->getLeft());
        $this->assertInstanceOf(DiceRoll::class, $result->getRight());
    }

    public function testMismatchOperatorsShouldThrow()
    {
        $this->expectException(\Exception::class);
        $this->parser->parse('(10%+');
    }
}
