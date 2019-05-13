<?php

declare(strict_types=1);

namespace FragSeb\Container;

use Closure;
use FragSeb\Container\Exception\DependencyException;
use FragSeb\Container\Exception\Exception;
use FragSeb\Container\Exception\NotFoundException;
use ReflectionClass;
use ReflectionParameter;
use function array_filter;
use function array_keys;
use function array_map;
use function array_values;
use function is_callable;
use function is_null;
use function is_object;
use function sprintf;

final class Container implements ContainerInterface
{
    /**
     * @var ServiceBag[]
     */
    private $services = [];

    public function set(string $id, $service = null, array $parameters = []): ContainerInterface
    {
        $parameters = array_map(static function (string $name, $value) {
            return new ParameterBag($name, $value);
        }, array_keys($parameters), array_values($parameters));

        $this->services[$id] = new ServiceBag($service ?? $id, ...$parameters);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException(sprintf('The service with the id `%s` cannot be found', $id));
        }

        $service = $this->services[$id];

        if (!is_callable($service->getService()) && is_object($service->getService())) {
            return $service->getService();
        }

        $service->setService($this->resolve($service->getService(), $service->getParameters()));

        return $this->services[$id]->getService();
    }

    /**
     * {@inheritDoc}
     */
    public function has($id): bool
    {
        return isset($this->services[$id]);
    }

    /**
     * {@inheritDoc}
     */
    public function resolve($service, array $parameters)
    {
        if ($service instanceof Closure) {
            return $service($this, $parameters);
        }

        $reflector = new ReflectionClass($service);

        if (!$reflector->isInstantiable()) {
            throw new Exception(sprintf('The class `%s` cannot be initialized', $service));
        }

        $constructor = $reflector->getConstructor();

        if (is_null($constructor)) {
            return $reflector->newInstance();
        }

        return $reflector->newInstanceArgs(
            $this->getDependencies($constructor->getParameters(), $parameters)
        );
    }

    /**
     * @param ReflectionParameter[] $parameters
     * @param ParameterBagInterface[] $defaultParameters
     *
     * @return array
     *
     * @throws DependencyException
     */
    public function getDependencies(array $parameters, array $defaultParameters): array
    {
        return array_map(function (ReflectionParameter $parameter) use ($defaultParameters) {
            $dependency = $parameter->getClass();

            $defaultParameter = array_values(array_filter(
                $defaultParameters,
                static function (ParameterBagInterface $parameterBag) use ($parameter): bool {
                    return $parameter->getName() === $parameterBag->getName();
                }
            ));

            if ($defaultParameter !== []) {
                return $defaultParameter[0]->getValue();
            }

            if (!$dependency) {
                if ($parameter->isDefaultValueAvailable()) {
                    return $parameter->getDefaultValue();
                }

                throw new DependencyException(
                    sprintf('The class dependency for the argument `%s` cannot be resolved', $parameter->name)
                );
            }

            return $this->set($dependency->name)->get($dependency->name);
        }, $parameters);
    }
}
