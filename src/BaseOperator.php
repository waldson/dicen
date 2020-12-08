<?php

declare(strict_types=1);

namespace W5n\Dicen;

abstract class BaseOperator implements Token
{
    abstract public function isRightAssociative(): bool;
    abstract public function getSymbol(): string;
    abstract public function getPrecedence(): int;
    abstract public function compare(Operator $other): int;
}
