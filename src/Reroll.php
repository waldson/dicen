<?php

namespace W5n\Dicen;

class Reroll
{
    private $comparator;
    private $threshold;

    const COMPARATOR_EQUAL            = '=';
    const COMPARATOR_GREATER          = '>';
    const COMPARATOR_GREATER_OR_EQUAL = '>=';
    const COMPARATOR_LESSER           = '<';
    const COMPARATOR_LESSER_OR_EQUAL  = '<=';

    public function __construct($comparator, $threshold)
    {
        if (!in_array(
            $comparator,
            [
                self::COMPARATOR_EQUAL,
                self::COMPARATOR_GREATER,
                self::COMPARATOR_GREATER_OR_EQUAL,
                self::COMPARATOR_LESSER,
                self::COMPARATOR_LESSER_OR_EQUAL,
            ]
        )) {
            throw new \Exception('Invalid comparator.');
        }

        $this->comparator = $comparator;
        $this->threshold = $threshold;
    }

    public function shouldReroll(int $value): bool
    {
        switch ($this->comparator) {
            case self::COMPARATOR_EQUAL:
                return $value == $this->threshold;
            case self::COMPARATOR_GREATER:
                return $value > $this->threshold;
            case self::COMPARATOR_GREATER_OR_EQUAL:
                return $value >= $this->threshold;
            case self::COMPARATOR_LESSER:
                return $value < $this->threshold;
            case self::COMPARATOR_LESSER_OR_EQUAL:
                return $value <= $this->threshold;
        }
    }

    /**
     * Getter for comparator
     *
     * @return string
     */
    public function getComparator()
    {
        return $this->comparator;
    }

    /**
     * Getter for threshold
     *
     * @return string
     */
    public function getThreshold()
    {
        return $this->threshold;
    }
}
