<?php

namespace ProxyManager\ProxyGenerator\RemoteObject\MethodGenerator;

use ProxyManager\Generator\MagicMethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use ReflectionClass;
use Zend\Code\Generator\PropertyGenerator;
/**
 * Magic `__get` for remote objects
 *
 * @author Vincent Blanchon <blanchon.vincent@gmail.com>
 * @license MIT
 */
class MagicGet extends MagicMethodGenerator
{
    /**
     * Constructor
     * @param ReflectionClass                        $originalClass
     * @param \Zend\Code\Generator\PropertyGenerator $adapterProperty
     *
     * @throws \Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public function __construct(ReflectionClass $originalClass, PropertyGenerator $adapterProperty)
    {
        parent::__construct($originalClass, '__get', [new ParameterGenerator('name')]);
        $this->setDocBlock('@param string $name');
        $this->setBody('$return = $this->' . $adapterProperty->getName() . '->call(' . var_export($originalClass->getName(), true) . ', \'__get\', array($name));' . "\n\n" . 'return $return;');
    }
}