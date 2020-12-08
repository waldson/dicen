<?php

declare(strict_types=1);

namespace W5n\Dicen;

class Number implements Token
{
    private $value;
    private $position;

    public function __construct($value, int $position = 0)
    {
        $this->value    = $value;
        $this->position = $position;
    }

    public function getValue(?Context $context = null): int
    {
        return $this->value;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
