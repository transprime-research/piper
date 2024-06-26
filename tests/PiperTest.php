<?php

declare(strict_types=1);

namespace Piper\Tests;

use PHPUnit\Framework\TestCase;
use Transprime\Piper\{Piper, Exceptions\PiperException};

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
            ->to(function ($name) {
                return strtoupper($name);
            })
            ->up();

        $this->assertSame('NAME', $result);
    }

    public function testClosureCanBeRunOnPipedResult()
    {
        piper('name')
            ->to(function ($name) {
                return strtoupper($name);
            })
            ->up(function ($result) {
                return $this->assertSame('NAME', $result);
            });
    }

    public function testManyPipeTo()
    {
        piper('NAME')
            ->to(function ($name) {
                return strtolower($name);
            })
            ->to(function ($name) {
                return ucfirst($name);
            })
            ->up(function ($result) {
                return $this->assertSame('Name', $result);
            });
    }

    public function testPipeAPipedResult()
    {
        $result = piper('NAME')
            ->to(function ($name) {
                return strtolower($name);
            })
            ->to(function ($name) {
                return ucfirst($name);
            })
            ->up(function ($result) {
                return \piper($result)->to(function () use ($result) {
                    return [$result];
                })->up();
            });

        $this->assertSame(['Name'], $result);
    }

    public function testPiperAcceptsClosure()
    {
        $result = piper(function () {
            return 'NAME';
        })->to(function ($name) {
            return strtolower($name);
        })->up();

        $this->assertSame('name', $result);
    }

    public function testPipeMethodCannotBeCalledMoreThanOnce()
    {
        $this->expectException(PiperException::class);
        $this->expectExceptionMessage('on() must be called only once');

        piper(function () {
            return 'NAME';
        })
            ->pipe('1')
            ->to(function ($name) {
                return strtolower($name);
            })
            ->up();
    }

    public function testPipeAndToMethodMustBeCalledBeforeUp()
    {
        $this->expectException(PiperException::class);
        $this->expectExceptionMessage('on() must be called and to() at least once');

        piper(function () {
            return 'NAME';
        })->up();
    }

    public function testPipeMethodAcceptsCallable()
    {
        // test global method
        $result = piper('NAME', 'strtolower')
            ->to(function ($name) {
                return ucfirst($name);
            })
            ->up();

        $this->assertSame('Name', $result);

        // test class method
        $result2 = piper('NAME', StrManipulator::class . '::strToLower')
            ->to(function ($name) {
                return ucfirst($name);
            })
            ->up();

        $this->assertSame('Name', $result2);

        // test array class and method
        $result2 = piper('NAME', [StrManipulator::class, 'strToLower'])
            ->to(function ($name) {
                return ucfirst($name);
            })->up();

        $this->assertSame('Name', $result2);

        // test class object
        $result2 = piper('NAME', new StrManipulator())
            ->to(function ($name) {
                return ucfirst($name);
            })->up();

        $this->assertSame('Name', $result2);
    }

    public function testFunctionCanBeRetrievedAtTheNextPipe()
    {
        piper(function () {
            return function () {
                return function () {
                    return function () {
                        return 'ade';
                    };
                };
            };
        })
            ->to(function ($fn) {
                return $fn();
            }) //Executes the first function
            ->to(function ($fn) {
                return $fn();
            }) //Executes the second function
            ->to(function ($fn) {
                return $fn();
            }) //Executes the third function
            ->to(function ($name) {
                return ucfirst($name);
            }) //Executes the fourth function
            ->up(function ($result) {
                $this->assertSame('Ade', $result);
            }); //manipulates the result
    }

    public function testAcceptingFunctionAndParametersInToMethod()
    {
        $this->assertSame('ADE', piper('ade')->to('strtoupper')->up());

        $this->assertSame(
            10,
            piper(['ade', 1])
                ->to(function ($data) {
                    return array_filter($data, static function ($val): bool {
                        return is_int($val);
                    });
                })
                ->to('array_merge', [2, 3, 4])
                ->to('array_values')
                ->to('array_sum')
                ->up()
        );

        $this->assertSame(
            ['ADE'],
            piper(['name' => 'ade', 'hobby' => 'coding'])
                ->to('array_flip')
                ->to('array_keys')
                ->to('array_map', function ($val) {
                    return strtoupper($val);
                })
                ->to('array_intersect', [0 => 'ADE'])()
        );
    }

    public function testMakePiperInvokableWithoutCallingUpMethod()
    {
        $result = piper('name')
            ->to(function ($name) {
                return ucfirst($name);
            })();

        $this->assertSame('Name', $result);
    }

    public function testPiperOnStaticCreation()
    {
        $this->assertEquals(
            'n,a,m,e',
            Piper::on('NAME')
                ->to('str_split')
                ->to('implode', ',')
                ->to('strtolower')
                ->up()
        );
    }

    public function testStringPassedIntoFirstPipeShouldBeTreatedAsString()
    {
        $this->assertEquals(
            'STRMANIPULATOR::STRTOLOWER',
            Piper::on('StrManipulator::strToLower')
                ->to('strtoupper')
                ->up()
        );
    }

    private function runIfOnPHP81()
    {
        return (float)phpversion() >= 8.1;
    }

    public function testPiperWithPHP81FirstClassCallable()
    {
        $this->assertEquals(['ADE'],
            piper(['name' => 'ade', 'hobby' => 'coding'])
                ->to(array_flip(...))
                ->to(array_keys(...))
                ->to(array_map(...), fn($val) => strtoupper($val))
                ->to(array_intersect(...), [0 => 'ADE'])(),
        );

        $this->assertEquals(['H', 'E', 'L', 'L', 'O', ' ', 'W', 'O', 'R', 'L', 'D'],
            piper("Hello World")
                ->to(htmlentities(...))
                ->to(str_split(...))
                ->to(array_map(...), fn(string $part) => strtoupper($part))()
        );
    }

    public function testPiperWithLn()
    {
        $this->assertEquals(['H', 'E', 'L', 'L', 'O', ' ', 'W', 'O', 'R', 'L', 'D'],
            piper("Hello World")
                ->ln(
                    htmlentities(...),
                    str_split(...),
                    [array_map(...), strtoupper(...)],
                )
        );
    }

    public function testPiperWithDucter()
    {
        $this->assertEquals(['H', 'E', 'L', 'L', 'O', ' ', 'W', 'O', 'R', 'L', 'D'],
            ducter(
                "Hello World",
                htmlentities(...),
                str_split(...),
                [array_map(...), strtoupper(...)],
            )
        );
    }

    public function testCallablePiper()
    {
        $this->assertEquals(['H', 'E', 'L', 'L', 'O', ' ', 'W', 'O', 'R', 'L', 'D'],
            _p("Hello World")
            (htmlentities(...))
            (str_split(...))
            (array_map(...), strtoupper(...))()
        );
    }

    public function testUnderscoredPiper()
    {
        $this->assertEquals(['H', 'E', 'L', 'L', 'O', ' ', 'W', 'O', 'R', 'L', 'D'],
            _p("Hello World")
                ->_(htmlentities(...))
                ->_(str_split(...))
                ->_(array_map(...), strtoupper(...))()
        );
    }

    public function testPPiper()
    {
        $this->assertEquals(['H', 'E', 'L', 'L', 'O', ' ', 'W', 'O', 'R', 'L', 'D'],
            _p("Hello World")
                ->p(htmlentities(...))
                ->p(str_split(...))
                ->p(array_map(...), strtoupper(...))()
        );
    }

    public function testFnPiper()
    {
        $this->assertEquals(['H', 'E', 'L', 'L', 'O', ' ', 'W', 'O', 'R', 'L', 'D'],
            _p("Hello World")
                ->fn(htmlentities(...))
                ->fn(str_split(...))
                ->fn(array_map(...), strtoupper(...))
                ->fn()
        );
    }

    public function testIndexPiper()
    {
        $this->assertEquals(['H', 'E', 'L', 'L', 'O', ' ', 'W', 'O', 'R', 'L', 'D'],
            _p("Hello World")
                [htmlentities(...)]
                [str_split(...)]
                [[array_map(...), strtoupper(...)]]()
        );
    }

    public function testProxiedPiper()
    {
        $this->assertEquals(['H', 'E', 'L', 'L', 'O', ' ', 'W', 'O', 'R', 'L', 'D'],
            _p("Hello World")
                ->htmlentities()
                ->str_split()
                ->array_map(strtoupper(...))()
        );
    }

    public function testProxiedPiperCall()
    {
        $this->assertEquals(['D', 'L', 'R', 'O', 'W', ' ', 'O', 'L', 'L', 'E', 'H'],
            _p("HELLO WORLD")
                ->htmlentities()
                ->str_split()
                ->array_reverse()
                ->array_values()
                ->up()
        );
    }
}

class StrManipulator
{
    public function __invoke(string $value)
    {
        return $this->strToLower($value);
    }

    public static function strToLower(string $value = '')
    {
        return strtolower($value);
    }
}