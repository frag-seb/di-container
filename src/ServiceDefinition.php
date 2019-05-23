<?php

declare(strict_types=1);

namespace FragSeb\Container;

use Closure;
use function array_filter;

final class ServiceDefinition implements ServiceBagInterface
{
    /**
     * @var string|object|Closure
     */
    private $service;

    /**
     * @var array|ParameterBagInterface[]
     */
    private $parameters;

    public function __construct($service, ParameterBagInterface ...$parameters)
    {
        $this->service = $service;
        $this->parameters = array_filter($parameters);
    }

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * {@inheritdoc}
     */
    public function setService($service): void
    {
        $this->service = $service;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
