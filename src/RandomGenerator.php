<?php

declare(strict_types=1);

namespace W5n\Dicen;

interface RandomGenerator
{
    public function generate(int $min, int $max);
}
