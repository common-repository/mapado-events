<?php

namespace ProxyManager\ProxyGenerator\Util;

use ReflectionClass;
use ReflectionProperty;
/**
 * DTO containing the list of all non-static proxy properties and utility methods to access them
 * in various formats/collections
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
final class Properties
{
    /**
     * @var array|\ReflectionProperty[]
     */
    private $properties;
    /**
     * @param ReflectionProperty[] $properties
     */
    private function __construct(array $properties)
    {
        $this->properties = $properties;
    }
    public static function fromReflectionClass(ReflectionClass $reflection)
    {
        $class = $reflection;
        $properties = [];
        do {
            $properties = array_merge($properties, array_values(array_filter($class->getProperties(), function (ReflectionProperty $property) use($class) {
                return $class->getName() === $property->getDeclaringClass()->getName() && !$property->isStatic();
            })));
        } while ($class = $class->getParentClass());
        return new self($properties);
    }
    /**
     * @param string[] $excludedProperties
     */
    public function filter(array $excludedProperties)
    {
        $properties = $this->getInstanceProperties();
        foreach ($excludedProperties as $propertyName) {
            unset($properties[$propertyName]);
        }
        return new self($properties);
    }
    /**
     * @return ReflectionProperty[] indexed by the property internal visibility-aware name
     */
    public function getPublicProperties()
    {
        $publicProperties = [];
        foreach ($this->properties as $property) {
            if ($property->isPublic()) {
                $publicProperties[$property->getName()] = $property;
            }
        }
        return $publicProperties;
    }
    /**
     * @return ReflectionProperty[] indexed by the property internal visibility-aware name (\0*\0propertyName)
     */
    public function getProtectedProperties()
    {
        $protectedProperties = [];
        foreach ($this->properties as $property) {
            if ($property->isProtected()) {
                $protectedProperties["\0*\0" . $property->getName()] = $property;
            }
        }
        return $protectedProperties;
    }
    /**
     * @return ReflectionProperty[] indexed by the property internal visibility-aware name (\0ClassName\0propertyName)
     */
    public function getPrivateProperties()
    {
        $privateProperties = [];
        foreach ($this->properties as $property) {
            if ($property->isPrivate()) {
                $declaringClass = $property->getDeclaringClass()->getName();
                $privateProperties["\0" . $declaringClass . "\0" . $property->getName()] = $property;
            }
        }
        return $privateProperties;
    }
    /**
     * @return ReflectionProperty[] indexed by the property internal visibility-aware name (\0*\0propertyName)
     */
    public function getAccessibleProperties()
    {
        return array_merge($this->getPublicProperties(), $this->getProtectedProperties());
    }
    /**
     * @return ReflectionProperty[][] indexed by class name and property name
     */
    public function getGroupedPrivateProperties()
    {
        $propertiesMap = [];
        foreach ($this->getPrivateProperties() as $property) {
            $class =& $propertiesMap[$property->getDeclaringClass()->getName()];
            $class[$property->getName()] = $property;
        }
        return $propertiesMap;
    }
    /**
     * @return ReflectionProperty[] indexed by the property internal visibility-aware name (\0*\0propertyName)
     */
    public function getInstanceProperties()
    {
        return array_merge($this->getAccessibleProperties(), $this->getPrivateProperties());
    }
}