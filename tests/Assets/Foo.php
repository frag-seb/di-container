<?php

declare(strict_types=1);

namespace Tests\FragSeb\Container\Assets;

use stdClass;

class Foo
{
    /**
     * @var string|null
     */
    private $name;

    /**
     * @var stdClass
     */
    private $foo;

    /**
     * Constructor.
     */
    public function __construct(stdClass $foo, ?string $name = null)
    {
        $this->name = $name;
        $this->foo = $foo;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getFoo(): stdClass
    {
        return $this->foo;
    }

    public function setFoo(stdClass $foo): void
    {
        $this->foo = $foo;
    }
}
