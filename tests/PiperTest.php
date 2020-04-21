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

    public function testPipeMethodAcceptsCallable()
    {
        // test global method
        $result = piper('NAME', 'strtolower')
            ->to(fn($name) => ucfirst($name))
            ->up();

        $this->assertSame('Name', $result);

        // test class method
        $result2 = piper('NAME', StrManipulator::class.'::strToLower')
            ->to(fn($name) => ucfirst($name))
            ->up();

        $this->assertSame('Name', $result2);

        // test array class and method
        $result2 = piper('NAME', [StrManipulator::class, 'strToLower'])
            ->to(fn($name) => ucfirst($name))
            ->up();

        $this->assertSame('Name', $result2);

        // test class object
        $result2 = piper('NAME', new StrManipulator())
            ->to(fn($name) => ucfirst($name))
            ->up();

        $this->assertSame('Name', $result2);
    }

    public function testFunctionCanBeRetrievedAtTheNextPipe()
    {
        piper(fn() => fn() => fn() => fn() => 'ade')
            ->to(fn($fn) => $fn()) //Executes the first function
            ->to(fn($fn) => $fn()) //Executes the second function
            ->to(fn($fn) => $fn()) //Executes the third function
            ->to(fn($name) => ucfirst($name)) //Executes the fourth function
            ->up(fn($result) => $this->assertSame('Ade', $result)); //manipulates the result
    }

    public function testAcceptingFunctionAndParametersInToMethod()
    {
        $this->assertSame('ADE', piper('ade')->to('strtoupper')->up());

        $this->assertSame(
            [1],
            piper(['ade', 1])
                ->to('array_filter', fn($val) => is_int($val))
                ->to('array_values')
                ->up()
        );

        $this->assertSame(
            ['ADE'],
            piper(['ade'])
                ->to(fn($names, $fn) => $fn(fn($name) => strtoupper($name), $names), 'array_map')
                ->up()
        );
    }
}

class StrManipulator {

    public function __invoke(string $value)
    {
        return $this->strToLower($value);
    }

    public static function strToLower(string $value)
    {
        return strtolower($value);
    }
}