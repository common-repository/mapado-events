<?php

namespace ProxyManager\GeneratorStrategy;

use Zend\Code\Generator\ClassGenerator;
/**
 * Generator strategy that generates the class body
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
class BaseGeneratorStrategy implements GeneratorStrategyInterface
{
    /**
     * {@inheritDoc}
     */
    public function generate(ClassGenerator $classGenerator)
    {
        return $classGenerator->generate();
    }
}