<?php

namespace ProxyManager\Signature\Exception;

use ReflectionClass;
use UnexpectedValueException;
/**
 * Exception for no found signatures
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class MissingSignatureException extends UnexpectedValueException implements ExceptionInterface
{
    public static function fromMissingSignature(ReflectionClass $class, array $parameters, $expected)
    {
        return new self(sprintf('No signature found for class "%s", expected signature "%s" for %d parameters', $class->getName(), $expected, count($parameters)));
    }
}