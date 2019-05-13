<?php

declare(strict_types=1);

namespace FragSeb\Container;

interface ParameterBagInterface
{
    public function getName(): string;

    /**
     * @return mixed
     */
    public function getValue();
}
