<?php

namespace ProxyManager\ProxyGenerator\LazyLoadingGhost\MethodGenerator;

use ProxyManager\Generator\MagicMethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\PrivatePropertiesMap;
use ProxyManager\ProxyGenerator\LazyLoadingGhost\PropertyGenerator\ProtectedPropertiesMap;
use ProxyManager\ProxyGenerator\PropertyGenerator\PublicPropertiesMap;
use ProxyManager\ProxyGenerator\Util\PublicScopeSimulator;
use ReflectionClass;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\PropertyGenerator;
/**
 * Magic `__unset` method for lazy loading ghost objects
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class MagicUnset extends MagicMethodGenerator
{
    /**
     * @var string
     */
    private $callParentTemplate = <<<'PHP'
%s

if (isset(self::$%s[$name])) {
    unset($this->$name);

    return;
}

if (isset(self::$%s[$name])) {
    // check protected property access via compatible class
    $callers      = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
    $caller       = isset($callers[1]) ? $callers[1] : [];
    $object       = isset($caller['object']) ? $caller['object'] : '';
    $expectedType = self::$%s[$name];

    if ($object instanceof $expectedType) {
        unset($this->$name);

        return;
    }

    $class = isset($caller['class']) ? $caller['class'] : '';

    if ($class === $expectedType || is_subclass_of($class, $expectedType) || $class === 'ReflectionProperty') {
        unset($this->$name);

        return;
    }
} elseif (isset(self::$%s[$name])) {
    // check private property access via same class
    $callers = debug_backtrace(\DEBUG_BACKTRACE_PROVIDE_OBJECT, 2);
    $caller  = isset($callers[1]) ? $callers[1] : [];
    $class   = isset($caller['class']) ? $caller['class'] : '';

    static $accessorCache = [];

    if (isset(self::$%s[$name][$class])) {
        $cacheKey = $class . '#' . $name;
        $accessor = isset($accessorCache[$cacheKey])
            ? $accessorCache[$cacheKey]
            : $accessorCache[$cacheKey] = \Closure::bind(function ($instance) use ($name) {
                unset($instance->$name);
            }, null, $class);

        return $accessor($this);
    }

    if ('ReflectionProperty' === $class) {
        $tmpClass = key(self::$%s[$name]);
        $cacheKey = $tmpClass . '#' . $name;
        $accessor = isset($accessorCache[$cacheKey])
            ? $accessorCache[$cacheKey]
            : $accessorCache[$cacheKey] = \Closure::bind(function ($instance) use ($name) {
                unset($instance->$name);
            }, null, $tmpClass);

        return $accessor($this);
    }
}

%s
PHP;
    /**
     * @param ReflectionClass        $originalClass
     * @param PropertyGenerator      $initializerProperty
     * @param MethodGenerator        $callInitializer
     * @param PublicPropertiesMap    $publicProperties
     * @param ProtectedPropertiesMap $protectedProperties
     * @param PrivatePropertiesMap   $privateProperties
     *
     * @throws \Zend\Code\Generator\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     */
    public function __construct(ReflectionClass $originalClass, PropertyGenerator $initializerProperty, MethodGenerator $callInitializer, PublicPropertiesMap $publicProperties, ProtectedPropertiesMap $protectedProperties, PrivatePropertiesMap $privateProperties)
    {
        parent::__construct($originalClass, '__unset', [new ParameterGenerator('name')]);
        $override = $originalClass->hasMethod('__unset');
        $parentAccess = 'return parent::__unset($name);';
        if (!$override) {
            $parentAccess = PublicScopeSimulator::getPublicAccessSimulationCode(PublicScopeSimulator::OPERATION_UNSET, 'name');
        }
        $this->setBody(sprintf($this->callParentTemplate, '$this->' . $initializerProperty->getName() . ' && $this->' . $callInitializer->getName() . '(\'__unset\', array(\'name\' => $name));', $publicProperties->getName(), $protectedProperties->getName(), $protectedProperties->getName(), $privateProperties->getName(), $privateProperties->getName(), $privateProperties->getName(), $parentAccess));
    }
}