<?php

namespace ProxyManager\Inflector;

use ProxyManager\Inflector\Util\ParameterHasher;
/**
 * {@inheritDoc}
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
final class ClassNameInflector implements ClassNameInflectorInterface
{
    /**
     * @var string
     */
    protected $proxyNamespace;
    /**
     * @var int
     */
    private $proxyMarkerLength;
    /**
     * @var string
     */
    private $proxyMarker;
    /**
     * @var \ProxyManager\Inflector\Util\ParameterHasher
     */
    private $parameterHasher;
    /**
     * @param string $proxyNamespace
     */
    public function __construct($proxyNamespace)
    {
        $this->proxyNamespace = $proxyNamespace;
        $this->proxyMarker = '\\' . static::PROXY_MARKER . '\\';
        $this->proxyMarkerLength = strlen($this->proxyMarker);
        $this->parameterHasher = new ParameterHasher();
    }
    /**
     * {@inheritDoc}
     */
    public function getUserClassName($className)
    {
        $className = ltrim($className, '\\');
        if (false === ($position = strrpos($className, $this->proxyMarker))) {
            return $className;
        }
        return substr($className, $this->proxyMarkerLength + $position, strrpos($className, '\\') - ($position + $this->proxyMarkerLength));
    }
    /**
     * {@inheritDoc}
     */
    public function getProxyClassName($className, array $options = [])
    {
        return $this->proxyNamespace . $this->proxyMarker . $this->getUserClassName($className) . '\\Generated' . $this->parameterHasher->hashParameters($options);
    }
    /**
     * {@inheritDoc}
     */
    public function isProxyClassName($className)
    {
        return false !== strrpos($className, $this->proxyMarker);
    }
}