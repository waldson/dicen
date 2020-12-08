# Dicen

Parser for dice rolls.


## Instalation

```
composer install w5n/dicen
```


## Usage

```php
use W5n\Dicen\DefaultRandomGenerator;
use W5n\Dicen\DiceEngine;
use W5n\Dicen\DiceParser;

$parser          = new DiceParser();
$randomGenerator = new DefaultRandomGenerator();
$engine          = new DiceEngine($parser, $randomGenerator);

// Dice mode
$engine->roll('5d4+3');

// Math mode
$engine->roll('120+10*33-45');

// Mixed mode
$engine->roll('2d6-4+45-2d10+8');


// Invalid roll throws exception
try {
    $engine->roll('invalid');
} catch (\Exception $ex) {
    //...
}

// Syntax tree
$ast = $parser->parse('2d6+25-1d4-1');

```


## TO-DO

- [ ] Use a custom exception
- [ ] Exploding Dices
- [ ] Keep or Drop some highest/lowest dices
- [ ] Show roll details

## References

- [Roll20's dice reference](https://wiki.roll20.net/Dice_Reference)
- [Shunting-yard algorithm](https://en.wikipedia.org/wiki/Shunting-yard_algorithm)
