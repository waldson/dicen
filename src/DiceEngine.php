<?php

declare(strict_types=1);

namespace W5n\Dicen;

class DiceEngine
{
    private $parser;
    private $randomGenerator;

    public function __construct(Parser $parser, RandomGenerator $generator)
    {
        $this->parser          = $parser;
        $this->randomGenerator = $generator;
    }

    public function roll(string $roll, $params = [])
    {
        return $this->evaluate($this->parser->parse($roll), $params);
    }

    private function evaluate(Token $token, array $params = [])
    {
        return $token->getValue($this->createContext($params));
    }

    public function createContext(array $params): Context
    {
        return new Context(
            $this,
            $this->parser,
            $this->randomGenerator,
            $params
        );
    }
}
