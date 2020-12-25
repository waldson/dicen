<?php

declare(strict_types=1);

namespace W5n\Dicen;

class DiceDiscard
{
    const TYPE_NONE         = 'none';
    const TYPE_KEEP_HIGHEST = 'keep_highest';
    const TYPE_KEEP_LOWEST  = 'keep_lowest';
    const TYPE_DROP_HIGHEST = 'drop_highest';
    const TYPE_DROP_LOWEST  = 'drop_lowest';

    private $count = 0;
    private $type  = null;

    public function __construct(string $type = self::TYPE_NONE, $count = 0)
    {
        if (!in_array(
            $type,
            [
                self::TYPE_DROP_HIGHEST,
                self::TYPE_DROP_LOWEST,
                self::TYPE_KEEP_HIGHEST,
                self::TYPE_KEEP_LOWEST,
                self::TYPE_NONE
            ]
        )) {
            throw new \Exception('Invalid discard type: ' . $type);
        }

        $this->type  = $type;
        $this->count = $count;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
