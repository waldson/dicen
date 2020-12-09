<?php

declare(strict_types=1);


namespace W5n\Dicen;

use W5n\Scanero\Scanner;

/**
 * This parser uses Shunting-yard algorithm (Shunting-yard algorithm).
 *
 * @package W5n\Dicen
 * */
class DiceParser implements Parser
{
    /**@var Scanner*/
    private $scanner = null;

    /**@var \SplStack*/
    private $valueStack;

    /**@var \SplStack*/
    private $operatorStack;


    /**@var int*/
    private $terminalTokenCount = 0;

    public function parse(string $roll): Token
    {
        if (empty($this->scanner)) {
            $this->scanner = new Scanner($roll);
        } else {
            $this->scanner->reset($roll);
        }
        $this->operatorStack = new \SplStack();
        $this->valueStack = new \SplStack();
        $this->terminalTokenCount = 0;

        return $this->doParse();
    }

    private function doParse(): Token
    {
        while (!$this->scanner->consumed()) {
            $this->scanner->consumeWhitespaces();

            if ($this->isNumber()) {
                $this->valueStack->push($this->parseExpression());
            } elseif ($this->isOperator()) {
                $operator = $this->operatorFromSymbol($this->scanner->consume());

                while (
                    !$this->operatorStack->isEmpty()
                    && $this->operatorStack->top() != '('
                    && ($this->operatorStack->top()->compare($operator) == 1
                        ||
                        ($this->operatorStack->top()->compare($operator) == 0
                            && !$operator->isRightAssociative()))
                ) {
                    $oldOperator = $this->operatorStack->pop();

                    $right = $this->valueStack->pop();
                    $left = $this->valueStack->pop();
                    $this->valueStack->push(new Operation($oldOperator, $left, $right));
                }
                $this->operatorStack->push($operator);
            } elseif ($this->isOpenParens()) {
                $this->operatorStack->push('(');
                $this->scanner->consume();
            } elseif ($this->isCloseParens()) {
                $this->scanner->consume();
                while (
                    !$this->operatorStack->isEmpty()
                    && $this->operatorStack->top() != '('
                ) {
                    $operator = $this->operatorStack->pop();
                    $right    = $this->valueStack->pop();
                    $left     = $this->valueStack->pop();

                    $this->valueStack->push(new Operation($operator, $left, $right));
                }

                if ($this->operatorStack->isEmpty()) {
                    throw new \Exception('Mismatching parenthesis.');
                }

                $this->operatorStack->pop();
            } else {
                throw new \Exception(
                    sprintf(
                        'ParserError: unexpected "%s"',
                        $this->scanner->peek()
                    )
                );
            }
        }

        while (!$this->operatorStack->isEmpty()) {
            $operator = $this->operatorStack->pop();
            $right    = $this->valueStack->pop();
            $left     = $this->valueStack->pop();

            /* if (empty($left) || empty($right)) { */
            /*     throw new \Exception("Invalid expression."); */
            /* } */

            $this->valueStack->push(new Operation($operator, $left, $right));
        }

        //stack should have only one element here
        return $this->valueStack->pop();
    }

    private function operatorFromSymbol($symbol): Operator
    {
        switch ($symbol) {
            case '+':
                return new Operator('+', 1, false, $this->terminalTokenCount++);
            case '-':
                return new Operator('-', 1, false, $this->terminalTokenCount++);
            case '*':
            case 'x':
            case 'X':
                return new Operator('*', 2, false, $this->terminalTokenCount++);
            case '/':
                return new Operator('/', 2, false, $this->terminalTokenCount++);
            case '%':
                return new Operator('%', 2, false, $this->terminalTokenCount++);
            case '^':
                return new Operator('^', 3, true, $this->terminalTokenCount++);
            default:
                throw new \Exception("Invalid operator: " . $symbol . '.');
        }
    }

    private function consumeNumber(): int
    {
        $this->scanner->consumeWhitespaces();
        $result = $this->scanner->consumeWhile('#[0-9]#');

        if ($result === null || strlen($result) === 0) {
            throw new \Exception(
                sprintf(
                    'Expected number. Got "%s".',
                    $this->scanner->peek()
                )
            );
        }

        return intval($result);
    }

    private function parseExpression(): Token
    {
        $number = $this->consumeNumber();

        if ($this->isD()) {
            return $this->parseDice($number);
        }

        return new Number($number, $this->terminalTokenCount++);
    }

    private function parseDice($diceCount): Token
    {
        $this->scanner->consumeAny('d', 'D');

        $diceFaces = $this->consumeNumber();

        $discard = null;
        $rerolls = [];

        while ($this->hasAnyDiceModifier()) {
            if ($this->canBeDiscard()) {
                if (!empty($discard)) {
                    throw new \Exception('Syntax error: Keep and drops can only be called once per roll.');
                }

                $discard = $this->parseDiscard($diceCount);
            } elseif ($this->isReroll()) {
                $rerolls[] = $this->parseReroll();
            }
        }

        $modifier = 0;

        if ($this->isOperator()) {
            $this->scanner->savePosition();
            $sign  = $this->scanner->consume();

            $modifierSign = 1;



            if (($sign == '+' || $sign == '-') && !$this->scanner->matches('(')) {
                $modifierSign = $sign == '+' ? 1 : -1;

                $right   = $this->parseExpression();

                if (!($right instanceof Number)) {
                    $this->scanner->loadPosition();
                    return new DiceRoll(
                        $diceCount,
                        $diceFaces,
                        $modifier,
                        null,
                        $this->terminalTokenCount++,
                        $discard,
                        $rerolls
                    );
                }

                $this->scanner->popSavedPosition();

                $modifier = $right->getValue() * $modifierSign;
            } else {
                $this->scanner->loadPosition();

                return new DiceRoll(
                    $diceCount,
                    $diceFaces,
                    $modifier,
                    null,
                    $this->terminalTokenCount++,
                    null,
                    $rerolls
                );
            }
        }

        $label = null;

        if ($this->scanner->matches('[')) {
            $label = $this->consumeLabel();
        }

        return new DiceRoll(
            $diceCount,
            $diceFaces,
            $modifier,
            $label,
            $this->terminalTokenCount++,
            $discard,
            $rerolls
        );
    }


    private function parseDiscard(int $diceCount): ?DiceDiscard
    {
        if (!$this->scanner->matchesAny('k', 'd', 'K', 'D')) {
            return null;
        }
        $isKeep         = strtolower($this->scanner->consume()) == 'k';
        $highestOrLower = null;

        if ($this->scanner->matchesAny('h', 'H', 'l', 'L')) {
            $highestOrLower = strtolower($this->scanner->consume());
        } else {
            $highestOrLower = $isKeep ? 'h' : 'l';
        }

        $isHighest = $highestOrLower == 'h';

        $discardType = null;

        if ($isKeep) {
            $discardType = $isHighest ? DiceDiscard::TYPE_KEEP_HIGHEST : DiceDiscard::TYPE_KEEP_LOWEST;
        } else {
            $discardType = $isHighest ? DiceDiscard::TYPE_DROP_HIGHEST : DiceDiscard::TYPE_DROP_LOWEST;
        }

        $count = $this->consumeNumber();

        if ($count >= $diceCount) {
            $message = $isKeep
                ? 'You must keep at least one dice.'
                : 'You can not drop all of your dices.';

            throw new \Exception($message);
        }

        return new DiceDiscard($discardType, $count);
    }

    private function parseReroll(): Reroll
    {
        $this->scanner->consumeAny('r', 'R');
        $comparator = '=';

        if ($this->isComparator()) {
            $comparator = $this->consumeComparator();
        }

        $threshold = $this->consumeNumber();

        return new Reroll($comparator, $threshold);
    }


    private function consumeLabel(): string
    {
        $this->scanner->consume('[');
        $label = $this->scanner->consumeUnless('#\]#');
        $this->scanner->consume(']');
        return $label;
    }

    private function isBasicOperator()
    {
        return $this->isPlusOrMinus() || $this->scanner->matchesAny('*', '/', 'x', 'X');
    }

    private function isPlusOrMinus()
    {
        return $this->scanner->matchesAny('+', '-');
    }

    private function isNumber()
    {
        return $this->scanner->matchesAny(
            '1',
            '2',
            '3',
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            '0'
        );
    }

    private function isOperator()
    {
        return $this->isBasicOperator() || $this->scanner->matchesAny('^', '%');
    }

    private function isComparator()
    {
        return $this->scanner->matchesAny('>=', '<=', '=', '>', '<');
    }

    private function consumeComparator(): string
    {
        return $this->scanner->consumeAny('>=', '<=', '=', '>', '<');
    }

    public function isOpenParens()
    {
        return $this->scanner->matches('(');
    }

    public function isCloseParens()
    {
        return $this->scanner->matches(')');
    }

    public function canBeDiscard()
    {
        return $this->scanner->matchesAny('k', 'K', 'D', 'd');
    }

    public function isReroll()
    {
        return $this->scanner->matchesAny('r', 'R');
    }

    public function hasAnyDiceModifier()
    {
        return $this->canBeDiscard() || $this->isReroll();
    }

    private function isD()
    {
        return $this->scanner->matchesAny('d', 'D');
    }
}
