<?php

namespace Transprime\Piper;

trait On
{
    public function ln(callable|array ...$functions)
    {
        foreach ($functions as $function) {
            $callables = is_array($function) ? $function : [$function];
            $this->to(...$callables);
        }

        return $this->up();
    }
}