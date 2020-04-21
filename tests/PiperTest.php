<?php

namespace Piper\Tests;

use PHPUnit\Framework\TestCase;
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
}