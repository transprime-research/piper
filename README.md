# piper
PHP Pipe function execution with values from initial call like F#
> Pipe Like a PRO :ok:

## Installation

- `composer require transprime-research/piper`

## Quick Usage

Let us take an array and do the following:

- flip the array to make the keys become the values and vice versa
- get the new keys
- change (the keys now) values to to upper case
- take the exact item with `[0 => 'ADE']`
 
```php
piper(['name' => 'ade', 'hobby' => 'coding'])
    ->to('array_flip')
    ->to('array_keys')
    ->to('array_map', fn($val) => strtoupper($val))
    ->to('array_intersect', [0 => 'ADE'])(); //returns ['ADE']
```

Instead of:

```php
array_intersect(
    array_map(
        fn($val) => strtoupper($val),
        array_keys(
            array_flip(['name' => 'ade', 'hobby' => 'coding'])
        )
    ),
    [0 => 'ADE']
); //returns ['ADE']
```

Or:

```php
$arr = array_flip(['name' => 'ade', 'hobby' => 'coding']); // or array_values
$arr = array_keys($arr);
$arr = array_map(fn($val) => strtoupper($val), $arr);
$arr = array_intersect($arr, [0 => 'ADE']);

//$arr is ['ADE']
```

## Other Usages

```php
use Transprime\Piper\Piper;

// Normal
$piper = new Piper();
$piper->on(['AA'])->to('strtolower')->up();

// Better
$piper->on(['AA'])->to('strtolower')();

```

`piper()` function is a helper function. It is normally called like so:

```php

// Nifty
piper(['AA'])->to('strtolower')();
```

Piper `on()` method accepts a callable instance on the second parameter that would be immediately on the first parameter: 

Say we have this class:

Second callable parameter to `on()` method, which 'NAME' shall first be called on:

```php
// test global method
piper('NAME', 'strtolower') // NAME becomes name
    ->to(fn($name) => ucfirst($name))
    ->up();
```

Also accepts a class with an accessible method name:

Say we have this class:
```php
class StrManipulator
{
    public function __invoke(string $value)
    {
        return $this->strToLower($value);
    }

    public static function strToLower(string $value)
    {
        return strtolower($value);
    }
}
```

`StrManipulator` class can be passed in with a method like so:

```php
// test class method
piper('NAME', StrManipulator::class . '::strToLower')
    ->to(fn($name) => ucfirst($name))
    ->up();

// test array class and method
piper('NAME', [StrManipulator::class, 'strToLower'])
    ->to(fn($name) => ucfirst($name))
    ->up();
```

Even callable object is allowed like so:

```php
// test class object
piper('NAME', new StrManipulator()) // A class that implements __invoke
    ->to(fn($name) => ucfirst($name))
    ->up();
```

Please see `\Piper\Tests\PiperTest` for more examples.

## Coming Soon

- Calling piper statically
```php
// Good
Piper::on(['AA'])->to('strtolower')->up();

//Better
Piper::on(['AA'])->to('strtolower')();
```

- Backward Pipe with `fro()` looking like:

```php
piper('array_intersect', [0 => 'ADE'])
    ->fro('array_map', fn($val) => strtoupper($val))
    ->fro('array_keys')
    ->fro('array_flip')
    ->fro(['name' => 'ade', 'age' => 5])();
```

> Api implementation to be decided

## Similar packages

- https://packagist.org/packages/yuyat/piper
- https://packagist.org/packages/solidworx/piper

## Licence

MIT (See LICENCE file)