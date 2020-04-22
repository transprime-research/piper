# php-pipe
PHP Pipe function execution with values from initial call like F#
> Pipe Like a PRO :ok:

## Quick Usage

```php
piper(['name' => 'ade', 'age' => 5])
    ->to('array_flip')
    ->to('array_keys')
    ->to('array_map', fn($val) => strtoupper($val))
    ->to('array_intersect', [0 => 'ADE'])(); //returns ['ADE']
```

## Installation

- `composer require transprime-research/piper`

The example in #Quick Usage would normally be:

```php
array_intersect(
    array_map(
        fn($val) => strtoupper($val),
        array_keys(
            array_flip(['name' => 'ade', 'age' => 5])
        )
    ),
    [0 => 'ADE']
); //returns ['ADE']
```

Or:

```php
$arr = array_flip(['name' => 'ade', 'age' => 5]); // or array_values
$arr = array_keys($arr);
$arr = array_map(fn($val) => strtoupper($val), $arr);
$arr = array_intersect($arr, [0 => 'ADE']);

//$arr is ['ADE']
```

## Other Usages

`piper()` function is a helper function. It is normally called like so:

```php
use Transprime\Piper\Piper;

// Normal
$piper = new Piper();
$piper->on(['AA'])->to('strtolower')->up();

// Good
Piper::on(['AA'])->to('strtolower')->up();

//Better
Piper::on(['AA'])->to('strtolower')();

// Nifty
piper(['AA'])->to('strtolower')();
```

Second callable parameter to `pipe()` method, which 'NAME' shall first be called on:

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

```php
// test global method
piper('NAME', 'strtolower') // NAME becomes name
    ->to(fn($name) => ucfirst($name))
    ->up();

// test class method
piper('NAME', StrManipulator::class . '::strToLower')
    ->to(fn($name) => ucfirst($name))
    ->up();

// test array class and method
piper('NAME', [StrManipulator::class, 'strToLower'])
    ->to(fn($name) => ucfirst($name))
    ->up();

// test class object
piper('NAME', new StrManipulator()) // A class that implements __invoke
    ->to(fn($name) => ucfirst($name))
    ->up();
```

Please see `\Piper\Tests\PiperTest` for more examples.

## Coming Soon

Backward Piping with `fro()` looking like:

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