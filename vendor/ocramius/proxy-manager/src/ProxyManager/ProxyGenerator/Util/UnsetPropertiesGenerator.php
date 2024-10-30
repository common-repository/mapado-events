<?php

namespace ProxyManager\ProxyGenerator\Util;

/**
 * Generates code necessary to unset all the given properties from a particular given instance string name
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
final class UnsetPropertiesGenerator
{
    private static $closureTemplate = <<<'PHP'
\Closure::bind(function (\%s $instance) {
    %s
}, $%s, %s)->__invoke($%s);
PHP;
    public static function generateSnippet(Properties $properties, $instanceName)
    {
        return self::generateUnsetAccessiblePropertiesCode($properties, $instanceName) . self::generateUnsetPrivatePropertiesCode($properties, $instanceName);
    }
    private static function generateUnsetAccessiblePropertiesCode(Properties $properties, $instanceName)
    {
        $accessibleProperties = $properties->getAccessibleProperties();
        if (!$accessibleProperties) {
            return '';
        }
        return self::generateUnsetStatement($accessibleProperties, $instanceName) . "\n\n";
    }
    private static function generateUnsetPrivatePropertiesCode(Properties $properties, $instanceName)
    {
        $groups = $properties->getGroupedPrivateProperties();
        if (!$groups) {
            return '';
        }
        $unsetClosureCalls = [];
        /* @var $privateProperties \ReflectionProperty[] */
        foreach ($groups as $privateProperties) {
            /* @var $firstProperty \ReflectionProperty */
            $firstProperty = reset($privateProperties);
            $unsetClosureCalls[] = self::generateUnsetClassPrivatePropertiesBlock($firstProperty->getDeclaringClass(), $privateProperties, $instanceName);
        }
        return implode("\n\n", $unsetClosureCalls) . "\n\n";
    }
    private static function generateUnsetClassPrivatePropertiesBlock(\ReflectionClass $declaringClass, array $properties, $instanceName)
    {
        $declaringClassName = $declaringClass->getName();
        return sprintf(self::$closureTemplate, $declaringClassName, self::generateUnsetStatement($properties, 'instance'), $instanceName, var_export($declaringClassName, true), $instanceName);
    }
    private static function generateUnsetStatement(array $properties, $instanceName)
    {
        return 'unset(' . implode(', ', array_map(function (\ReflectionProperty $property) use($instanceName) {
            return '$' . $instanceName . '->' . $property->getName();
        }, $properties)) . ');';
    }
}