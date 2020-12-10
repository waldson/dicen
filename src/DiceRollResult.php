<?php

declare(strict_types=1);

namespace W5n\Dice;

class DiceRollResult
{
    private $label;
    private $faces;
    private $value;
    private $dropped  = false;
    private $reroll   = false;
    private $critical = false;
    private $failure  = false;

    public function __construct($label, $faces, $value, $dropped, $reroll, $critical, $failure)
    {
        $this->label    = $label;
        $this->faces    = $faces;
        $this->value    = $value;
        $this->dropped  = $dropped;
        $this->reroll   = $reroll;
        $this->critical = $critical;
        $this->failure  = $failure;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getFaces(): int
    {
        return $this->faces;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getDropped(): bool
    {
        return $this->dropped;
    }

    public function isReroll(): bool
    {
        return $this->reroll;
    }

    public function isCritical(): bool
    {
        return $this->critical;
    }

    public function isFailure(): bool
    {
        return $this->failure;
    }
}
