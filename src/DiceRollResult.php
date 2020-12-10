<?php

declare(strict_types=1);

namespace W5n\Dicen;

class DiceRollResult
{
    private $label;
    private $faces;
    private $value;
    private $dropped   = false;
    private $reroll    = false;
    private $critical  = false;
    private $failure   = false;
    private $diceIndex = 0;

    public function __construct(
        $label,
        $faces,
        $value,
        $dropped,
        $reroll,
        $critical,
        $failure,
        $diceIndex
    ) {
        $this->label     = $label;
        $this->faces     = $faces;
        $this->value     = $value;
        $this->dropped   = $dropped;
        $this->reroll    = $reroll;
        $this->critical  = $critical;
        $this->failure   = $failure;
        $this->diceIndex = $diceIndex;
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

    public function isDropped(): bool
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

    public function getDiceIndex(): int
    {
        return $this->diceIndex;
    }
}
