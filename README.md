# Dicen

Parser for dice rolls.


## Usage

```php
$parser          = new DiceParser();
$randomGenerator = new DefaultRandomGenerator();
$engine          = new DiceEngine(
    $parser,
    $randomGenerator
);

//dice mode
$engine->roll('5d4+3');

//math mode
$engine->roll('120+10*33-45');

//mixed mode
$engine->roll('2d6-4+45-2d10+8');


// invalid rolls throw exception
try {
    $engine->roll('invalid');
} catch (\Exception $ex) {
    //...
}

```


## TO-DO

- [ ] Use a custom exception
- [ ] Exploding Dices
- [ ] Keep or Drop some highest/lowest dices
- [ ] Show roll details
