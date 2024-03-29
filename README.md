<p align="center">
<img src="https://github.com/transprime-research/assets/blob/master/piper/twitter_header_photo_2.png">
</p>

<p align="center">
<a href="https://travis-ci.org/transprime-research/piper"> <img src="https://travis-ci.org/transprime-research/piper.svg?branch=master" alt="Build Status"/></a>
<a href="https://packagist.org/packages/transprime-research/piper"> <img src="https://poser.pugx.org/transprime-research/piper/v/stable" alt="Latest Stable Version"/></a>
<a href="https://packagist.org/packages/transprime-research/piper"> <img src="https://poser.pugx.org/transprime-research/piper/downloads" alt="Total Downloads"/></a>
<a href="https://packagist.org/packages/transprime-research/piper"> <img src="https://poser.pugx.org/transprime-research/piper/v/unstable" alt="Latest Unstable Version"/></a>
<a href="https://packagist.org/packages/transprime-research/piper"> <img src="https://poser.pugx.org/transprime-research/piper/d/monthly" alt="Latest Monthly Downloads"/></a>
  <a href="https://packagist.org/packages/transprime-research/piper"> <img src="https://poser.pugx.org/transprime-research/piper/license" alt="License"/></a>
</p>

# piper
PHP Pipe function execution with values from initial call like F#
> Pipe Like a PRO :ok:

## Quick Usage

Let us take an array and do the following:

- flip the array to make the keys become the values and vice versa
- get the new keys
- change (the keys now) values to upper case
- take the exact item with `[0 => 'ADE']`
 
```php
piper(['name' => 'ade', 'hobby' => 'coding'])
    ->to('array_flip')
    ->to('array_keys')
    ->to('array_map', fn($val) => strtoupper($val))
    ->to('array_intersect', [0 => 'ADE'])(); //returns ['ADE']
```

Or this in PHP 8.1+
Getting `['H', 'E', 'L', 'L', 'O', ' ', 'W', 'O', 'R', 'L', 'D']` examples:

Use line - as in the line in Pipe-"line"
```php
piper("Hello World")
    ->ln(
        htmlentities(...),
        str_split(...),
        [array_map(...), fn(string $part) => strtoupper($part)],
    );
```

Think about ductape on a pipe. Ducter allows such neat calls to your functions
```php
ducter(
    "Hello World",
    htmlentities(...),
    str_split(...),
    [array_map(...), fn(string $part) => strtoupper($part)],
)
```

Call functions like an array:
```php
_p("Hello World")
    [htmlentities(...)]
    [str_split(...)]
    [[array_map(...), strtoupper(...)]]()
```

How about Closure() on Closure
```php
_p("Hello World")
    (htmlentities(...))
    (strtoupper(...))
    (str_split(...))
    (array_map(...), strtoupper(...))()
```

Cleaner with underscore `_`
```php
_p("Hello World")
    ->_(htmlentities(...))
    ->_(str_split(...))
    ->_(array_map(...), strtoupper(...)));
```

Shortcut to `pipe()` is `p()`
```php
_p("Hello World")
    ->p(htmlentities(...))
    ->p(str_split(...))
    ->p(array_map(...), strtoupper(...))()
```

PHP 7.4 `fn()` like
```php
_p("Hello World")
    ->fn(htmlentities(...))
    ->fn(str_split(...))
    ->fn(array_map(...), strtoupper())
    ->fn()
```

Proxied call to globally available functions
```php
_p("Hello World")
    ->htmlentities()
    ->str_split()
    ->array_map(strtoupper(...))()
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
> PS: You can still use the old `function() { return v; }`, `fn()` is the new short arrow function in PHP 7.4+ See: https://www.php.net/manual/en/functions.arrow.php

## Installation

- `composer require transprime-research/piper`

## Requirement
Minimum Requirement
- PHP 7.2 +
- Composer

## Other Usages

```php
use Transprime\Piper\Piper;

// Normal
$piper = new Piper();
$piper->pipe(['AA'])->to('implode')->up();

// Better
$piper->on(['AA'])->to('implode')();

```

`piper()` function is a helper function. It is normally called like so:

```php
// Nifty
piper('AA')->to('strtolower')();

// Good
Piper::on('AA')->to('strtolower')->up();

//Better
Piper::on('AA')->to('strtolower')();
```

Piper `piper()` function accepts a callable instance on the second parameter that would be immediately on the first parameter: 

```php
// test global method
piper('NAME', 'strtolower') // NAME becomes name
    ->to(fn($name) => ucfirst($name))
    ->up();
```

Also accepts a class with an accessible method name. Say we have this class:

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

Please see `\Transprime\Piper\Tests\PiperTest` for more examples.

## Coming Soon

- Backward Pipe with `fro()` looking like:

```php
piper('array_intersect', [0 => 'ADE'])
    ->fro('array_map', fn($val) => strtoupper($val))
    ->fro('array_keys')
    ->fro('array_flip')
    ->fro(['name' => 'ade', 'age' => 5])();
```

> Api implementation to be decided

## Additional Information
See other packages in this series here:

- https://github.com/omitobi/conditional [A smart PHP if...elseif...else statement]
- https://github.com/transprime-research/arrayed [A smarter Array now like an object]
- https://github.com/transprime-research/attempt [A smart PHP try...catch statement]
- https://github.com/omitobi/carbonate [A smart Carbon + Collection package]
- https://github.com/omitobi/laravel-habitue [Jsonable Http Request(er) package with Collections response]

## Similar packages

- https://packagist.org/packages/yuyat/piper
- https://packagist.org/packages/solidworx/piper

## Licence

MIT (See LICENCE file)
