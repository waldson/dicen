<?php

declare(strict_types=1);

namespace W5n\Dicen;

class Operator extends BaseOperator
{
    private $symbol;
    private $precedence;
    private $rightAssociative;
    private $position;

    public function __construct($symbol, $precedence, $rightAssociative = false, $position = 0)
    {
        $this->symbol           = $symbol;
        $this->precedence       = $precedence;
        $this->rightAssociative = $rightAssociative;
        $this->position         = $position;
    }

    public function getPrecedence(): int
    {
        return $this->precedence;
    }

    public function getPosition(): int
    {
        return $this->position;
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

    public function __toString(): string
    {
        return $this->getSymbol();
    }
}
