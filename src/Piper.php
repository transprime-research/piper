<?php

namespace Piper;

use Piper\Exceptions\PiperException;

class Piper
{
    private array $piped = [];

    private array $extraParamsTo = [];
    private array $extraParamsFro = [];

    public function __invoke(callable $action = null)
    {
        return $this->up($action);
    }

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

    public function to(callable $action, ...$extraParameters)
    {
        $this->piped[] = $action;

        empty($extraParameters) ?: $this->extraParamsTo[count($this->piped) - 1] = $extraParameters;

        return $this;
    }

    /**
     * Backward Pipe
     *
     * @param callable $action
     * @param mixed ...$extraParameters
     * @return $this
     */
    public function fro(callable $action, ...$extraParameters)
    {
        $this->piped[] = $action;

        empty($extraParameters) ?: $this->extraParamsFro[count($this->piped) - 1] = $extraParameters;

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

            if ($this->extraParamsTo[$key] ?? false) {
                $result = $pipe(...$this->extraParamsTo[$key], ...[$result]);
            } elseif ($this->extraParamsFro[$key] ?? false) {
                $result = $pipe($result, ...$this->extraParamsFro[$key]);
            } else{
                $result = $pipe($result);
            }
        }

        return $action ? $action($result) : $result;
    }
}