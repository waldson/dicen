<?php

declare(strict_types=1);

namespace W5n\Dicen;

class Number implements Token
{
    private $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getValue(?Context $context = null): int
    {
        return $this->value;
    }
}
