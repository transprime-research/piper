<?php

namespace Piper;

class Piper
{
    private array $piped = [];

    public function pipe($value)
    {
        if (!empty($this->piped)) {
            throw new \Exception('pipe() must be called only once');
        }

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
        if (count($this->piped) < 2) {
            throw new \Exception('pipe() must be called and to() at least once');
        }

        $initial = $this->piped[0];

        if ($initial instanceof \Closure) {
            $initial = $initial();
        }

        foreach ($this->piped as $key => $pipe) {
            if ($key < 1) {
                continue;
            }

            if($pipe instanceof \Closure) {
                $initial = $pipe($initial);
            }
        }

        return $closure ? $closure($initial) : $initial;
    }
}