<?php

namespace Piper\Tests;

use PHPUnit\Framework\TestCase;
use Piper\Exceptions\PiperException;
use Piper\Piper;

class PiperTest extends TestCase
{
    public function testPiperIsCreated()
    {
        $this->assertIsObject(new Piper());
    }

    public function testPipeToUp()
    {
        $piper = new Piper();

        $result = $piper->pipe('name')
            ->to(fn($name) => strtoupper($name))
            ->up();

        $this->assertSame('NAME', $result);
    }

    public function testClosureCanBeRunOnPipedResult()
    {
        piper('name')
            ->to(fn($name) => strtoupper($name))
            ->up(fn($result) => $this->assertSame('NAME', $result));
    }

    public function testManyPipeTo()
    {
        piper('NAME')
            ->to(fn($name) => strtolower($name))
            ->to(fn($name) => ucfirst($name))
            ->up(fn($result) => $this->assertSame('Name', $result));
    }

    public function testPipeAPipedResult()
    {
        $result = piper('NAME')
            ->to(fn($name) => strtolower($name))
            ->to(fn($name) => ucfirst($name))
            ->up(fn($result) => \piper($result)->to(fn() => [$result])->up());

        $this->assertSame(['Name'], $result);
    }

    public function testPiperAcceptsClosure()
    {
        $result = piper(fn() => 'NAME')
            ->to(fn($name) => strtolower($name))
            ->up();

        $this->assertSame('name', $result);
    }

    public function testPipeMethodCannotBeCalledMoreThanOnce()
    {
        $this->expectException(PiperException::class);
        $this->expectExceptionMessage('pipe() must be called only once');

        piper(fn() => 'NAME')
            ->pipe('1')
            ->to(fn($name) => strtolower($name))
            ->up();
    }

    public function testPipeAndToMethodMustBeCalledBeforeUp()
    {
        $this->expectException(PiperException::class);
        $this->expectExceptionMessage('pipe() must be called and to() at least once');

        piper(fn() => 'NAME')
            ->up();
    }

    public function testPipeMethodAcceptsString()
    {
        $result = piper('NAME', 'strtolower')
            ->to(fn($name) => ucfirst($name))
            ->up();

        $this->assertSame('Name', $result);

        $result2 = piper('NAME', StrManipulator::class.'::strToLower')
            ->to(fn($name) => ucfirst($name))
            ->up();

        $this->assertSame('Name', $result2);
    }

    //tests
    // accept string as to() value to perform 'array_* method etc
}

class StrManipulator {

    public static function strToLower(string $value)
    {
        return strtolower($value);
    }
}