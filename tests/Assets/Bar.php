<?php

declare(strict_types=1);

namespace Tests\FragSeb\Container\Assets;

use stdClass;

final class Bar
{
    /**
     * @var Foo
     */
    private $foo;

    /**
     * @var stdClass
     */
    private $stdClass;

    /**
     * Constructor.
     */
    public function __construct(Foo $foo, stdClass $stdClass)
    {
        $this->foo = $foo;
        $this->stdClass = $stdClass;
    }

    public function getFoo(): Foo
    {
        return $this->foo;
    }

    public function setFoo(Foo $foo): void
    {
        $this->foo = $foo;
    }

    public function getStdClass(): stdClass
    {
        return $this->stdClass;
    }

    public function setStdClass(stdClass $stdClass): void
    {
        $this->stdClass = $stdClass;
    }
}
