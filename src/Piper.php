<?php

declare(strict_types=1);

namespace Transprime\Piper;

use Transprime\Piper\Exceptions\PiperException;

class Piper
{
    use On;
    use Link;

    private $piped = [];

    private $extraParameters = [];

    public function __invoke(callable $action = null)
    {
        return $this->up($action);
    }

    public static function on($value, callable $callback = null)
    {
        return (new static())->pipe($value, $callback);
    }

    public function pipe($value, callable $callback = null)
    {
        if (!empty($this->piped)) {
            throw new PiperException('on() must be called only once');
        }

        if (is_callable($callback)) {
            $value = $callback($value);
        }

        $this->piped[] = $value;

        return $this;
    }

    public function to(callable $action, ...$extraParameters)
    {
        $this->piped[] = $action;

        empty($extraParameters) ?: $this->extraParameters[count($this->piped) - 1] = $extraParameters;

        return $this;
    }

    public function up(callable $action = null)
    {
        if (count($this->piped) < 2) {
            throw new PiperException('on() must be called and to() at least once');
        }

        $result = $this->piped[0];

        if (is_callable($result) && !is_string($result)) {
            $result = $result();
        }

        foreach ($this->piped as $key => $pipe) {
            if ($key < 1) {
                continue;
            }

            if ($this->extraParameters[$key] ?? false) {
                $result = $pipe(...$this->extraParameters[$key], ...[$result]);
            } else {
                $result = $pipe($result);
            }
        }

        return $action ? $action($result) : $result;
    }
}