<?php

namespace ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator;

use ProxyManager\Generator\MethodGenerator;
use ProxyManager\ProxyGenerator\AccessInterceptorScopeLocalizer\MethodGenerator\Util\InterceptorGenerator;
use Zend\Code\Generator\PropertyGenerator;
use Zend\Code\Reflection\MethodReflection;
/**
 * Method with additional pre- and post- interceptor logic in the body
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class InterceptedMethod extends MethodGenerator
{
    /**
     * @throws \Zend\Code\Generator\Exception\InvalidArgumentException
     */
    public static function generateMethod(MethodReflection $originalMethod, PropertyGenerator $prefixInterceptors, PropertyGenerator $suffixInterceptors)
    {
        /* @var $method self */
        $method = static::fromReflectionWithoutBodyAndDocBlock($originalMethod);
        $forwardedParams = [];
        foreach ($originalMethod->getParameters() as $parameter) {
            $forwardedParams[] = ($parameter->isVariadic() ? '...' : '') . '$' . $parameter->getName();
        }
        $method->setBody(InterceptorGenerator::createInterceptedMethodBody('$returnValue = parent::' . $originalMethod->getName() . '(' . implode(', ', $forwardedParams) . ');', $method, $prefixInterceptors, $suffixInterceptors, $originalMethod));
        return $method;
    }
}