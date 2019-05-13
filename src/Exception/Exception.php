<?php

declare(strict_types=1);

namespace FragSeb\Container\Exception;

use Exception as BaseException;
use Psr\Container\ContainerExceptionInterface;

final class Exception extends BaseException implements ContainerExceptionInterface
{
}
