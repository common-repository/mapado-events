<?php

namespace ProxyManager\Inflector;

/**
 * Interface for a proxy- to user-class and user- to proxy-class name inflector
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
interface ClassNameInflectorInterface
{
    /**
     * Marker for proxy classes - classes containing this marker are considered proxies
     */
    const PROXY_MARKER = '__PM__';
    /**
     * Retrieve the class name of a user-defined class
     */
    public function getUserClassName($className);
    /**
     * Retrieve the class name of the proxy for the given user-defined class name
     *
     * @param string $className
     * @param array  $options   arbitrary options to be used for the generated class name
     */
    public function getProxyClassName($className, array $options = []);
    /**
     * Retrieve whether the provided class name is a proxy
     */
    public function isProxyClassName($className);
}