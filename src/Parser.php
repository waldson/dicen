<?php

declare(strict_types=1);

namespace W5n\Dicen;

interface Parser
{
    public function parse(string $roll): Token;
}
