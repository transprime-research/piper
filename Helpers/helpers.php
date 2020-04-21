<?php

if (! function_exists('piper')) {
    /**
     * New up a piped value
     *
     * @param $value
     * @return \Piper\Piper
     */
    function piper($value) {
        return (new \Piper\Piper())
            ->pipe($value);
    }
}