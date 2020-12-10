<?php

declare(strict_types=1);

namespace W5n\Dicen;

use W5n\Dicen\DiceRollResultBuilder;

class DiceRoll implements Token
{
    private $count     = 1;
    private $faces     = 6;
    private $modifier  = 0;
    private $label     = null;
    private $lastRoll  = [];
    private $position  = 0;
    private $discard   = null;
    private $rerolls   = [];
    private $explosive = false;


    public function __construct(
        int $count            = 1,
        int $faces            = 6,
        int $modifier         = 0,
        ?string $label        = null,
        int $position         = 0,
        ?DiceDiscard $discard = null,
        array $rerolls        = [],
        $explosive            = false
    ) {
        if ($faces == 0) {
            throw new \Exception('A dice cannot have 0 faces.');
        }

        $this->count     = $count;
        $this->faces     = $faces;
        $this->modifier  = $modifier;
        $this->label     = $label;
        $this->position  = $position;
        $this->discard   = $discard;
        $this->rerolls   = $rerolls;
        $this->explosive = $explosive;
    }

    public function roll(RandomGenerator $generator): int
    {
        $resultBuilder  = new DiceRollResultBuilder();
        $this->lastRoll = [];

        $diceIndex = 0;
        $sum       = 0;

        for ($i = 0; $i < $this->count; ++$i) {
            $rollCount = 0;
            $result = null;

            while ($rollCount == 0 || (!empty($result) && $this->shouldReroll($result->getValue()))) {
                $diceResult = $this->rollToBuilder($resultBuilder, $generator);
                $sum += $diceResult;

                $resultBuilder
                    ->withReroll($rollCount > 0)
                    ->withDiceIndex($diceIndex++);

                if ($this->shouldReroll($diceResult)) {
                    $resultBuilder->withDropped(true);
                }

                $this->lastRoll[] = $resultBuilder->build();

                $rollCount++;
            }
        }

        if (!empty($this->discard) && $this->discard->getType() !== DiceDiscard::TYPE_NONE) {
            dd("OK");
        }



        return $sum + $this->getModifier();
    }

    private function rollToBuilder(DiceRollResultBuilder $builder, RandomGenerator $generator): int
    {
        $builder->reset();
        $builder->withFaces($this->faces);

        if (!empty($this->label)) {
            $builder->withLabel($this->label);
        }

        $result = $generator->generate(1, $this->faces);

        $builder->withValue($result);

        $builder->withFailure($result == 1);
        $builder->withCritical($result == $this->faces);

        return $result;
    }

    private function shouldReroll(int $diceResult): bool
    {
        foreach ($this->getRerolls() as $reroll) {
            if ($reroll->shouldReroll($result)) {
                return true;
            }
        }

        return false;
    }

    public function getValue(?Context $context = null): int
    {
        return $this->roll($context->getRandomGenerator());
    }

    public function __toString()
    {
        $result = sprintf(
            '%sd%s',
            $this->count,
            $this->faces
        );

        if (!empty($this->modifier)) {
            if ($this->modifier > 0) {
                $result .= '+' . $this->modifier;
            } elseif ($this->modifier < 0) {
                $result .= '-' . abs($this->modifier);
            }
        }

        if (!empty($this->label)) {
            $result .= '[' . $this->label . ']';
        }

        return $result;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getDiceCount(): int
    {
        return $this->count;
    }

    public function getDiceFaces(): int
    {
        return $this->faces;
    }

    public function getModifier(): int
    {
        return $this->modifier;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function getDiscard(): DiceDiscard
    {
        return $this->discard;
    }

    public function getRerolls(): array
    {
        return $this->rerolls;
    }

    public function isExplosive(): bool
    {
        return $this->explosive;
    }
}
