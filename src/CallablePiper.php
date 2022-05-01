<?php

namespace Transprime\Piper;

class CallablePiper
{
    private Piper $piper;

    public function __construct(Piper $piper)
    {
        $this->piper = $piper->to(fn($vl) => $vl);
    }

    public function __invoke(callable|array $callable = null)
    {
        return $callable ? $this->to($callable) : $this->up();
    }

    public function to(callable $action, ...$extraParameters): static|null|string
    {
        $this->piper->to($action, ...$extraParameters);

        return $this;
    }

    public function up(callable $action = null)
    {
        return $this->piper->up($action);
    }
}