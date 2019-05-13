<?php

declare(strict_types=1);

namespace FragSeb\Container;

interface ServiceBagInterface
{
    /**
     * @return object|string
     */
    public function getService();

    /**
     * @param object $service
     */
    public function setService($service): void;

    public function getParameters(): array;
}
