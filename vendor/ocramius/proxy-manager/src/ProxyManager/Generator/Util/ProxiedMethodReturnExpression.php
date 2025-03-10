<?php

namespace ProxyManager\Generator\Util;

/**
 * Utility class to generate return expressions in method, given a method signature.
 *
 * This is required since return expressions may be forbidden by the method signature (void).
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
final class ProxiedMethodReturnExpression
{
    public static function generate($returnedValueExpression, \ReflectionMethod $originalMethod = null)
    {
        if ($originalMethod && 'void' === (string) $originalMethod->getReturnType()) {
            return $returnedValueExpression . ";\nreturn;";
        }
        return 'return ' . $returnedValueExpression . ';';
    }
}