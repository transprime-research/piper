<?php

declare(strict_types=1);

namespace Transprime\Piper;

class CallablePiper implements \ArrayAccess
{
    use Link;

    private Piper $piper;

    public function __construct(Piper $piper)
    {
        $this->piper = $piper->to(fn($vl) => $vl);
    }

    public function to(callable $action, ...$extraParameters): static
    {
        $this->piper->to($action, ...$extraParameters);

        return $this;
    }

    public function up(callable $action = null)
    {
        return $this->piper->up($action);
    }

    public function p(callable ...$callable)
    {
        return $this->__invoke(...$callable);
    }

    public function _(callable ...$callable)
    {
        return $this->__invoke(...$callable);
    }

    public function fn(callable ...$callable)
    {
        return $this->__invoke(...$callable);
    }

    public function offsetExists(mixed $offset): bool
    {
        return true;
    }

    public function offsetGet(mixed ...$offset): mixed
    {
        if (is_array($offset[0])) {
           return $this->__invoke(...$offset[0]);
        }

        return $this->__invoke(...$offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void {}

    public function offsetUnset(mixed $offset): void {}
}