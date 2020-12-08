<?php

declare(strict_types=1);

namespace W5n\Dicen;

class Operator extends BaseOperator
{
    private $symbol;
    private $precedence;
    private $rightAssociative;

    public function __construct($symbol, $precedence, $rightAssociative = false)
    {
        $this->symbol     = $symbol;
        $this->precedence = $precedence;
        $this->rightAssociative = $rightAssociative;
    }

    public function getPrecedence(): int
    {
        return $this->precedence;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function isRightAssociative(): bool
    {
        return $this->rightAssociative;
    }

    public function compare(Operator $other): int
    {
        if ($this->getPrecedence() > $other->getPrecedence()) {
            return 1;
        } elseif ($this->getPrecedence() < $other->getPrecedence()) {
            return -1;
        }

        return 0;
    }

    public function getValue(?Context $context = null): int
    {
        return 0;
    }
}
