<?php

namespace ProxyManager\Signature\Exception;

use ReflectionClass;
use UnexpectedValueException;
/**
 * Exception for invalid provided signatures
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class InvalidSignatureException extends UnexpectedValueException implements ExceptionInterface
{
    public static function fromInvalidSignature(ReflectionClass $class, array $parameters, $signature, $expected)
    {
        return new self(sprintf('Found signature "%s" for class "%s" does not correspond to expected signature "%s" for %d parameters', $signature, $class->getName(), $expected, count($parameters)));
    }
}