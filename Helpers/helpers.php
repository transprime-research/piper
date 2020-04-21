<?php

use Piper\Piper;

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
}