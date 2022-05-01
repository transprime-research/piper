<?php

namespace Transprime\Piper;

class CallablePiper
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
}