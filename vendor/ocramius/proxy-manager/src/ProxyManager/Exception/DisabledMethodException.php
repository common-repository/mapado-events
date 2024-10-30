<?php

namespace ProxyManager\Exception;

use BadMethodCallException;
/**
 * Exception for forcefully disabled methods
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class DisabledMethodException extends BadMethodCallException implements ExceptionInterface
{
    const NAME = __CLASS__;
    public static function disabledMethod($method)
    {
        return new self(sprintf('Method "%s" is forcefully disabled', $method));
    }
}