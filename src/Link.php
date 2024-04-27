<?php

declare(strict_types=1);

namespace Transprime\Piper;

/**
 * Add Methods doc blocks here if needed
 */
trait Link
{
    /**
     * @param callable|array|null $callable
     * @return mixed|self
     */
    public function __invoke(...$callable): mixed
    {
        return $callable ? $this->to(...$callable) : $this->up();
    }

    /**
     * @return mixed|self
     */
    public function __call(string $name, array $arguments)
    {
        return $this->__invoke($name, ...$arguments);
    }
}
