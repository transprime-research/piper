# php-pipe
PHP Pipe function execution with values from initial call like F#
> Pipe Like a PRO :ok:

##Usage
```php
piper(['ade','tobi'])
    ->to('array_flip')
    ->to('array_keys')
    ->to('array_map', fn($val) => strtoupper($val))
    ->to('array_intersect', [0 => 'ADE'])(); //returns ['ADE']
```