<?php

namespace Piper;

use Piper\Exceptions\PiperException;

class Piper
{
    private array $piped = [];

    public function pipe($value, callable $callback = null)
    {
        if (!empty($this->piped)) {
            throw new PiperException('pipe() must be called only once');
        }

        if (is_callable($callback)) {
            $value = $callback($value);
        }

        $this->piped[] = $value;

        return $this;
    }

    public function to(callable $action)
    {
        $this->piped[] = $action;

        return $this;
    }

    public function up(callable $action = null)
    {
        if (count($this->piped) < 2) {
            throw new PiperException('pipe() must be called and to() at least once');
        }

        $result = $this->piped[0];

        if (is_callable($result)) {
            $result = $result();
        }

        foreach ($this->piped as $key => $pipe) {
            if ($key < 1) {
                continue;
            }

            $result = $pipe($result);
        }

        return $action ? $action($result) : $result;
    }
}