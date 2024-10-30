<?php

namespace ProxyManager\Factory;

use ProxyManager\Proxy\VirtualProxyInterface;
use ProxyManager\ProxyGenerator\LazyLoadingValueHolderGenerator;
use ProxyManager\ProxyGenerator\ProxyGeneratorInterface;
/**
 * Factory responsible of producing virtual proxy instances
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class LazyLoadingValueHolderFactory extends AbstractBaseFactory
{
    /**
     * @var \ProxyManager\ProxyGenerator\LazyLoadingValueHolderGenerator|null
     */
    private $generator;
    public function createProxy($className, \Closure $initializer, array $proxyOptions = [])
    {
        $proxyClassName = $this->generateProxy($className, $proxyOptions);
        return $proxyClassName::staticProxyConstructor($initializer);
    }
    /**
     * {@inheritDoc}
     */
    protected function getGenerator()
    {
        return $this->generator ?: ($this->generator = new LazyLoadingValueHolderGenerator());
    }
}