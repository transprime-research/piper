<?php

namespace Transprime\Piper;

trait On
{
    /**
     * @param callable|array ...$functions
     * @return mixed|string
     * @throws Exceptions\PiperException
     */
    public function ln(...$functions)
    {
        foreach ($functions as $function) {
            $callables = is_array($function) ? $function : [$function];
            $this->to(...$callables);
        }

        return $this->up();
    }
}