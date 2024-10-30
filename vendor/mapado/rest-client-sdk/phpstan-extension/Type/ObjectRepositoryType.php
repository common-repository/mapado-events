<?php

namespace Mapado\RestClientSdk\PHPStan\Type;

use PHPStan\Type\ObjectType;
use PHPStan\Type\VerbosityLevel;
class ObjectRepositoryType extends ObjectType
{
    /** @var string */
    private $entityClass;
    public function __construct($entityClass, $repositoryClass)
    {
        parent::__construct($repositoryClass);
        $this->entityClass = $entityClass;
    }
    public function getEntityClass()
    {
        return $this->entityClass;
    }
    public function describe(VerbosityLevel $level)
    {
        return sprintf('%s<%s>', parent::describe($level), $this->entityClass);
    }
}