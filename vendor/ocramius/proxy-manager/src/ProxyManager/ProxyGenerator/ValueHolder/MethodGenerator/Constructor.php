<?php

namespace ProxyManager\ProxyGenerator\ValueHolder\MethodGenerator;

use ProxyManager\Generator\MethodGenerator;
use ProxyManager\ProxyGenerator\Util\Properties;
use ProxyManager\ProxyGenerator\Util\UnsetPropertiesGenerator;
use ReflectionClass;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Reflection\MethodReflection;
use Zend\Code\Reflection\ParameterReflection;
/**
 * The `__construct` implementation for lazy loading proxies
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class Constructor extends MethodGenerator
{
    /**
     * @throws \Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public static function generateMethod(ReflectionClass $originalClass, PropertyGenerator $valueHolder)
    {
        $originalConstructor = self::getConstructor($originalClass);
        /* @var $constructor self */
        $constructor = $originalConstructor ? self::fromReflectionWithoutBodyAndDocBlock($originalConstructor) : new self('__construct');
        $constructor->setBody('static $reflection;' . "\n\n" . 'if (! $this->' . $valueHolder->getName() . ') {' . "\n" . '    $reflection = $reflection ?: new \\ReflectionClass(' . var_export($originalClass->getName(), true) . ");\n" . '    $this->' . $valueHolder->getName() . ' = $reflection->newInstanceWithoutConstructor();' . "\n" . UnsetPropertiesGenerator::generateSnippet(Properties::fromReflectionClass($originalClass), 'this') . '}' . ($originalConstructor ? self::generateOriginalConstructorCall($originalConstructor, $valueHolder) : ''));
        return $constructor;
    }
    private static function generateOriginalConstructorCall(MethodReflection $originalConstructor, PropertyGenerator $valueHolder)
    {
        return "\n\n" . '$this->' . $valueHolder->getName() . '->' . $originalConstructor->getName() . '(' . implode(', ', array_map(function (ParameterReflection $parameter) {
            return ($parameter->isVariadic() ? '...' : '') . '$' . $parameter->getName();
        }, $originalConstructor->getParameters())) . ');';
    }
    /**
     * @param ReflectionClass $class
     *
     * @return MethodReflection|null
     */
    private static function getConstructor(ReflectionClass $class)
    {
        $constructors = array_map(function (\ReflectionMethod $method) {
            return new MethodReflection($method->getDeclaringClass()->getName(), $method->getName());
        }, array_filter($class->getMethods(), function (\ReflectionMethod $method) {
            return $method->isConstructor();
        }));
        return reset($constructors) ?: null;
    }
}