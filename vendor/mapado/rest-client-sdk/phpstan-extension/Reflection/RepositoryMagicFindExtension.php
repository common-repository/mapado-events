<?php

namespace Mapado\RestClientSdk\PHPStan\Reflection;

use PHPStan\Broker\Broker;
use PHPStan\Reflection\BrokerAwareExtension;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\Dummy\DummyMethodReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
class RepositoryMagicFindExtension implements MethodsClassReflectionExtension, BrokerAwareExtension
{
    /** @var Broker */
    private $broker;
    public function setBroker(Broker $broker)
    {
        $this->broker = $broker;
    }
    public function hasMethod(ClassReflection $classReflection, $methodName)
    {
        if (0 !== mb_strpos($methodName, 'findBy') && 0 !== mb_strpos($methodName, 'findOneBy')) {
            return false;
        }
        if ('Mapado\\RestClientSdk\\EntityRepository' === $classReflection->getName()) {
            return true;
        }
        if (!$this->broker->hasClass('Mapado\\RestClientSdk\\EntityRepository')) {
            return false;
        }
        return $classReflection->isSubclassOf('Mapado\\RestClientSdk\\EntityRepository');
    }
    public function getMethod(ClassReflection $classReflection, $methodName)
    {
        return new DummyMethodReflection($methodName);
    }
}