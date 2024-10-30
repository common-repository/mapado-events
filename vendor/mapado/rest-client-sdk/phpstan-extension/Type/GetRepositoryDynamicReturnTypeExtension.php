<?php

namespace Mapado\RestClientSdk\PHPStan\Type;

use PhpParser\Node\Expr\MethodCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\Constant\ConstantStringType;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\Type;
class GetRepositoryDynamicReturnTypeExtension implements DynamicMethodReturnTypeExtension
{
    /** @var string */
    private $sdkClientClass;
    /** @var ObjectMetadataResolver */
    private $metadataResolver;
    public function __construct($sdkClientClass, ObjectMetadataResolver $metadataResolver)
    {
        $this->sdkClientClass = $sdkClientClass;
        $this->metadataResolver = $metadataResolver;
    }
    public function getClass()
    {
        return $this->sdkClientClass;
    }
    public function isMethodSupported(MethodReflection $methodReflection)
    {
        return 'getRepository' === $methodReflection->getName();
    }
    public function getTypeFromMethodCall(MethodReflection $methodReflection, MethodCall $methodCall, Scope $scope)
    {
        if (0 === count($methodCall->args)) {
            return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
        }
        $argType = $scope->getType($methodCall->args[0]->value);
        if (!$argType instanceof ConstantStringType) {
            return new MixedType();
        }
        $objectName = $argType->getValue();
        $className = $this->metadataResolver->resolveClassnameForKey($objectName);
        $repositoryClass = $this->metadataResolver->getRepositoryClass($className);
        return new ObjectRepositoryType($className, $repositoryClass);
    }
}