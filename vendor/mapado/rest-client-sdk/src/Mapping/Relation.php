<?php

namespace Mapado\RestClientSdk\Mapping;

/**
 * Class Relation
 *
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class Relation
{
    const MANY_TO_ONE = 'ManyToOne';
    const ONE_TO_MANY = 'OneToMany';
    /**
     * @var string
     */
    private $serializedKey;
    /**
     * @var string
     */
    private $type;
    /**
     * @var string
     */
    private $targetEntity;
    public function __construct($serializedKey, $type, $targetEntity)
    {
        $this->serializedKey = $serializedKey;
        $this->type = $type;
        $this->targetEntity = $targetEntity;
    }
    public function getSerializedKey()
    {
        return $this->serializedKey;
    }
    public function setSerializedKey($serializedKey)
    {
        $this->serializedKey = $serializedKey;
        return $this;
    }
    public function getType()
    {
        return $this->type;
    }
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
    public function isOneToMany()
    {
        return self::ONE_TO_MANY === $this->getType();
    }
    public function isManyToOne()
    {
        return self::MANY_TO_ONE === $this->getType();
    }
    public function getTargetEntity()
    {
        return $this->targetEntity;
    }
    public function setTargetEntity($targetEntity)
    {
        $this->targetEntity = $targetEntity;
        return $this;
    }
}