<?php

declare(strict_types=1);

namespace W5n\Dicen;

interface Token
{
    public function getValue(?Context $context = null): int;
}
