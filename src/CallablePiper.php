<?php declare(strict_types=1);

namespace Transprime\Piper;

class CallablePiper implements \ArrayAccess
{
    private Piper $piper;

    public function __construct(Piper $piper)
    {
        $this->piper = $piper->to(fn($vl) => $vl);
    }

    /**
     * @param callable|array|null $callable
     * @return mixed|self
     */
    public function __invoke(callable|array ...$callable): mixed
    {
        return $callable ? $this->to(...$callable) : $this->up();
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

    /**
     * @return mixed|self
     */
    public function __call(string $name, array $arguments)
    {
        return $this->__invoke($name, ...$arguments);
    }
}