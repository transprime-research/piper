<?php

use Transprime\Piper\CallablePiper;
use Transprime\Piper\Piper;

if (! function_exists('piper')) {
    /**
     * New up a piped value
     *
     * @param $value
     * @param null $callback
     * @return Piper
     */
    function piper($value, $callback = null) {
        return (new Piper())
            ->pipe($value, $callback);
    }

    function ducter($value, callable|array ...$lines) {
        $piper = \piper($value);

        return $piper->ln(...$lines);
    }

    function _p($value) {
        return new CallablePiper(\piper($value));
    }
}