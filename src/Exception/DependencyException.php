<?php

declare(strict_types=1);

namespace FragSeb\Container\Exception;

use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;

final class DependencyException extends InvalidArgumentException implements ContainerExceptionInterface
{
}
