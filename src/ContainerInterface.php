<?php

declare(strict_types=1);

namespace FragSeb\Container;

use Closure;
use FragSeb\Container\Exception\DependencyException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface as BaseContainerInterface;
use ReflectionException;
use ReflectionParameter;

interface ContainerInterface extends BaseContainerInterface
{
    /**
     * @param string|object|Closure|null $service
     */
    public function add(string $id, $service = null, array $parameters = []): ContainerInterface;

    /**
     * @param mixed $service
     *
     * @return mixed|object
     *
     * @throws ContainerExceptionInterface|ReflectionException
     */
    public function resolve($service, array $parameters);

    /**
     * @param ReflectionParameter[] $parameters
     * @param ParameterBagInterface[] $defaultParameters
     *
     * @return array
     *
     * @throws DependencyException
     */
    public function getDependencies(array $parameters, array $defaultParameters): array;
}
