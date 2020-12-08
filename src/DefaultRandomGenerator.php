<?php

declare(strict_types=1);

namespace W5n\Dicen;

class DefaultRandomGenerator implements RandomGenerator
{
    public function generate(int $min, int $max)
    {
        return mt_rand($min, $max);
    }
}
