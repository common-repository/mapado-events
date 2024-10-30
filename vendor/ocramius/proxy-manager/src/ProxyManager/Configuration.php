<?php

namespace ProxyManager;

use ProxyManager\Autoloader\Autoloader;
use ProxyManager\Autoloader\AutoloaderInterface;
use ProxyManager\FileLocator\FileLocator;
use ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy;
use ProxyManager\GeneratorStrategy\GeneratorStrategyInterface;
use ProxyManager\Inflector\ClassNameInflector;
use ProxyManager\Inflector\ClassNameInflectorInterface;
use ProxyManager\Signature\ClassSignatureGenerator;
use ProxyManager\Signature\ClassSignatureGeneratorInterface;
use ProxyManager\Signature\SignatureChecker;
use ProxyManager\Signature\SignatureCheckerInterface;
use ProxyManager\Signature\SignatureGenerator;
use ProxyManager\Signature\SignatureGeneratorInterface;
/**
 * Base configuration class for the proxy manager - serves as micro disposable DIC/facade
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class Configuration
{
    const DEFAULT_PROXY_NAMESPACE = 'ProxyManagerGeneratedProxy';
    /**
     * @var string|null
     */
    protected $proxiesTargetDir;
    /**
     * @var string
     */
    protected $proxiesNamespace = self::DEFAULT_PROXY_NAMESPACE;
    /**
     * @var GeneratorStrategyInterface|null
     */
    protected $generatorStrategy;
    /**
     * @var AutoloaderInterface|null
     */
    protected $proxyAutoloader;
    /**
     * @var ClassNameInflectorInterface|null
     */
    protected $classNameInflector;
    /**
     * @var SignatureGeneratorInterface|null
     */
    protected $signatureGenerator;
    /**
     * @var SignatureCheckerInterface|null
     */
    protected $signatureChecker;
    /**
     * @var ClassSignatureGeneratorInterface|null
     */
    protected $classSignatureGenerator;
    public function setProxyAutoloader(AutoloaderInterface $proxyAutoloader)
    {
        $this->proxyAutoloader = $proxyAutoloader;
    }
    public function getProxyAutoloader()
    {
        return $this->proxyAutoloader ?: ($this->proxyAutoloader = new Autoloader(new FileLocator($this->getProxiesTargetDir()), $this->getClassNameInflector()));
    }
    public function setProxiesNamespace($proxiesNamespace)
    {
        $this->proxiesNamespace = $proxiesNamespace;
    }
    public function getProxiesNamespace()
    {
        return $this->proxiesNamespace;
    }
    public function setProxiesTargetDir($proxiesTargetDir)
    {
        $this->proxiesTargetDir = $proxiesTargetDir;
    }
    public function getProxiesTargetDir()
    {
        return $this->proxiesTargetDir ?: ($this->proxiesTargetDir = sys_get_temp_dir());
    }
    public function setGeneratorStrategy(GeneratorStrategyInterface $generatorStrategy)
    {
        $this->generatorStrategy = $generatorStrategy;
    }
    public function getGeneratorStrategy()
    {
        return $this->generatorStrategy ?: ($this->generatorStrategy = new EvaluatingGeneratorStrategy());
    }
    public function setClassNameInflector(ClassNameInflectorInterface $classNameInflector)
    {
        $this->classNameInflector = $classNameInflector;
    }
    public function getClassNameInflector()
    {
        return $this->classNameInflector ?: ($this->classNameInflector = new ClassNameInflector($this->getProxiesNamespace()));
    }
    public function setSignatureGenerator(SignatureGeneratorInterface $signatureGenerator)
    {
        $this->signatureGenerator = $signatureGenerator;
    }
    public function getSignatureGenerator()
    {
        return $this->signatureGenerator ?: ($this->signatureGenerator = new SignatureGenerator());
    }
    public function setSignatureChecker(SignatureCheckerInterface $signatureChecker)
    {
        $this->signatureChecker = $signatureChecker;
    }
    public function getSignatureChecker()
    {
        return $this->signatureChecker ?: ($this->signatureChecker = new SignatureChecker($this->getSignatureGenerator()));
    }
    public function setClassSignatureGenerator(ClassSignatureGeneratorInterface $classSignatureGenerator)
    {
        $this->classSignatureGenerator = $classSignatureGenerator;
    }
    public function getClassSignatureGenerator()
    {
        return $this->classSignatureGenerator ?: new ClassSignatureGenerator($this->getSignatureGenerator());
    }
}