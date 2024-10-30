<?php

namespace Mapado\RestClientSdk\Mapping;

/**
 * Class Attribute
 *
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class Attribute
{
    /**
     * @var string
     */
    private $serializedKey;
    /**
     * @var string
     */
    private $type;
    /**
     * @var bool
     */
    private $isIdentifier;
    /**
     * @var string
     */
    private $attributeName;
    /**
     * @throws \InvalidArgumentException
     */
    public function __construct($serializedKey, $attributeName = null, $type = null, $isIdentifier = false)
    {
        if (empty($serializedKey)) {
            throw new \InvalidArgumentException('attribute name must be set');
        }
        $this->serializedKey = $serializedKey;
        $this->attributeName = isset($attributeName) ? $attributeName : $this->serializedKey;
        $this->type = isset($type) ? $type : 'string';
        $this->isIdentifier = $isIdentifier;
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
    public function isIdentifier()
    {
        return $this->isIdentifier;
    }
    public function setIsIdentifier($isIdentifier)
    {
        $this->isIdentifier = $isIdentifier;
        return $this;
    }
    public function getAttributeName()
    {
        return $this->attributeName;
    }
    public function setAttributeName($attributeName)
    {
        $this->attributeName = $attributeName;
        return $this;
    }
}