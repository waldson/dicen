<?php

declare(strict_types=1);

namespace W5n\Dicen;

class Context
{
    private $engine;
    private $parser;
    private $randomGenerator;
    private $params;

    public function __construct(
        DiceEngine $engine,
        Parser $parser,
        RandomGenerator $randomGenerator,
        array $params = []
    ) {
        $this->engine          = $engine;
        $this->parser          = $parser;
        $this->randomGenerator = $randomGenerator;
        $this->params          = $params;
    }

    public function getEngine(): DiceEngine
    {
        return $this->engine;
    }

    public function getParser(): Parser
    {
        return $this->parser;
    }

    public function getRandomGenerator(): RandomGenerator
    {
        return $this->randomGenerator;
    }

    public function getParams(): array
    {
        return $this->params;
    }

    public function getParam($name, $defaultValue = null)
    {
        return $this->params[$name] ?? $defaultValue;
    }
}
