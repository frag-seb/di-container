<?php

declare(strict_types=1);

namespace Tests\FragSeb\Container\Assets;

final class Baz
{
    private $name;

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public function create(string $name)
    {
        return new self($name);
    }
}
