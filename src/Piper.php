<?php

namespace Piper;

class Piper
{
    private array $piped = [];

    public function pipe($value)
    {
        $this->piped[] = $value;

        return $this;
    }

    public function to(\Closure $closure)
    {
        $this->piped[] = $closure;

        return $this;
    }

    public function up(\Closure $closure = null)
    {
        if (! isset($this->piped[0])) {
            throw new \Exception('Initial pipe value not available');
        }

        $initial = $this->piped[0];

        foreach ($this->piped as $pipe) {
            if($pipe instanceof \Closure) {
                $initial = $pipe($initial);
            }
        }

        return $closure ? $closure($initial) : $initial;
    }
}