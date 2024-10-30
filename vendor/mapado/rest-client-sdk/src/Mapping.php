<?php

namespace Mapado\RestClientSdk;

use Mapado\RestClientSdk\Exception\MappingException;
use Mapado\RestClientSdk\Mapping\ClassMetadata;
/**
 * Class Mapping
 *
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class Mapping
{
    const DEFAULT_CONFIG = ['collectionKey' => 'hydra:member'];
    /**
     * @var string
     */
    private $idPrefix;
    /**
     * @var int
     */
    private $idPrefixLength;
    /**
     * @var array<ClassMetadata>
     */
    private $classMetadataList = [];
    /**
     * @var array
     */
    private $config;
    public function __construct($idPrefix = '', array $config = [])
    {
        $this->idPrefix = $idPrefix;
        $this->idPrefixLength = mb_strlen($idPrefix);
        $this->setConfig($config);
    }
    public function getIdPrefix()
    {
        return $this->idPrefix;
    }
    public function getConfig()
    {
        return $this->config;
    }
    public function setConfig(array $config)
    {
        $this->config = array_merge(self::DEFAULT_CONFIG, $config);
        return $this;
    }
    /**
     * @param array<ClassMetadata> $classMetadataList
     */
    public function setMapping(array $classMetadataList)
    {
        $this->classMetadataList = $classMetadataList;
        return $this;
    }
    /**
     * return a model class name for a given key
     */
    public function getModelName($key)
    {
        $this->checkMappingExistence($key, true);
        /** @var ClassMetadata */
        $classMetadata = $this->getClassMetadataByKey($key);
        return $classMetadata->getModelName();
    }
    /**
     * return the list of mapping keys
     *
     * @return array<string>
     */
    public function getMappingKeys()
    {
        return array_map(function (ClassMetadata $classMetadata) {
            return $classMetadata->getKey();
        }, $this->classMetadataList);
    }
    /**
     * get the key from an id (path)
     */
    public function getKeyFromId($id)
    {
        $key = $this->parseKeyFromId($id);
        if (null === $key) {
            throw new MappingException("Unable to parse key from id {$id}.");
        }
        $this->checkMappingExistence($key);
        return $key;
    }
    /**
     * @param string $modelName model name
     *
     * @throws MappingException
     */
    public function getKeyFromModel($modelName)
    {
        foreach ($this->classMetadataList as $classMetadata) {
            if ($modelName === $classMetadata->getModelName()) {
                return $classMetadata->getKey();
            }
        }
        throw new MappingException('Model name ' . $modelName . ' not found in mapping');
    }
    /**
     * getClassMetadata for model name
     *
     * @throws MappingException
     */
    public function getClassMetadata($modelName)
    {
        foreach ($this->classMetadataList as $classMetadata) {
            if ($modelName === $classMetadata->getModelName()) {
                return $classMetadata;
            }
        }
        throw new MappingException($modelName . ' model is not mapped');
    }
    /**
     * getClassMetadata for id
     */
    public function tryGetClassMetadataById($id)
    {
        $key = $this->parseKeyFromId($id);
        foreach ($this->classMetadataList as $classMetadata) {
            if ($key === $classMetadata->getKey()) {
                return $classMetadata;
            }
        }
        return null;
    }
    public function hasClassMetadata($modelName)
    {
        foreach ($this->classMetadataList as $classMetadata) {
            if ($modelName === $classMetadata->getModelName()) {
                return true;
            }
        }
        return false;
    }
    public function getClassMetadataByKey($key)
    {
        foreach ($this->classMetadataList as $classMetadata) {
            if ($key === $classMetadata->getKey()) {
                return $classMetadata;
            }
        }
        return null;
    }
    /**
     * Parse the key from an id (path)
     */
    private function parseKeyFromId($id)
    {
        $id = $this->removePrefix($id);
        $matches = [];
        if (1 === preg_match('|/([^/]+)/[^/]+$|', $id, $matches)) {
            return $matches[1];
        }
        return null;
    }
    private function checkMappingExistence($key, $checkModelName = false)
    {
        if (empty($key)) {
            throw new MappingException('key is not set');
        }
        $metadata = $this->getClassMetadataByKey($key);
        if (!$metadata) {
            throw new MappingException($key . ' key is not mapped');
        }
        if ($checkModelName) {
            if ('' === $metadata->getModelName()) {
                throw new MappingException($key . ' key is mapped but the model name is empty');
            }
        }
    }
    private function removePrefix($value)
    {
        if ($this->idPrefixLength > 0 && 0 === mb_strpos($value, $this->idPrefix)) {
            return mb_substr($value, $this->idPrefixLength);
        }
        return $value;
    }
}