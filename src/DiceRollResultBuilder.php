<?php

declare(strict_types=1);

namespace W5n\Dicen;

class DiceRollResultBuilder
{
    private $label;
    private $faces;
    private $value;
    private $dropped   = false;
    private $reroll    = false;
    private $critical  = false;
    private $failure   = false;
    private $diceIndex = 0;


    public static function fromDiceResult(DiceRollResult $result): DiceRollResultBuilder
    {
        $builder = new self();

        $builder->label     = $result->getLabel();
        $builder->faces     = $result->getFaces();
        $builder->value     = $result->getValue();
        $builder->dropped   = $result->isDropped();
        $builder->reroll    = $result->isReroll();
        $builder->reroll    = $result->isReroll();
        $builder->critical  = $result->isCritical();
        $builder->failure   = $result->isFailure();
        $builder->diceIndex = $result->getDiceIndex();

        return $builder;
    }

    public function withLabel(string $label): DiceRollResultBuilder
    {
        $this->label = $label;
        return $this;
    }

    public function withDiceIndex(int $diceIndex): DiceRollResultBuilder
    {
        $this->diceIndex = $diceIndex;
        return $this;
    }

    public function withFaces(int $faces): DiceRollResultBuilder
    {
        $this->faces = $faces;
        return $this;
    }

    public function withValue(int $value): DiceRollResultBuilder
    {
        $this->value = $value;
        return $this;
    }

    public function withDropped(bool $dropped): DiceRollResultBuilder
    {
        $this->dropped = $dropped;
        return $this;
    }

    public function withReroll(bool $reroll): DiceRollResultBuilder
    {
        $this->reroll = $reroll;
        return $this;
    }

    public function withCritical(bool $critical): DiceRollResultBuilder
    {
        $this->critical = $critical;
        return $this;
    }

    public function withFailure(bool $failure): DiceRollResultBuilder
    {
        $this->failure = $failure;
        return $this;
    }

    public function build(): DiceRollResult
    {
        if (!$this->isValid()) {
            throw new \Exception('Incomplete dice result. Fill at least faces and value.');
        }

        return new DiceRollResult(
            $this->label,
            $this->faces,
            $this->value,
            $this->dropped,
            $this->reroll,
            $this->critical,
            $this->failure,
            $this->diceIndex
        );
    }

    public function reset(): DiceRollResultBuilder
    {
        $this->label     = null;
        $this->faces     = null;
        $this->value     = null;
        $this->dropped   = false;
        $this->reroll    = false;
        $this->critical  = false;
        $this->failure   = false;
        $this->diceIndex = 0;

        return $this;
    }

    private function isValid(): bool
    {
        return !empty($this->faces) && !empty($this->value);
    }
}
