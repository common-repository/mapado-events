<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Zend\Code\Generator\DocBlock\Tag;

class VarTag extends AbstractTypeableTag implements TagInterface
{
    /**
     * @var string|null
     */
    private $variableName;
    /**
     * @param string|null     $variableName
     * @param string|string[] $types
     * @param string|null     $description
     */
    public function __construct($variableName = null, $types = [], $description = null)
    {
        if (null !== $variableName) {
            $this->variableName = ltrim($variableName, '$');
        }
        parent::__construct($types, $description);
    }
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'var';
    }
    /**
     * @internal this code is only public for compatibility with the
     *           @see \Zend\Code\Generator\DocBlock\TagManager, which
     *           uses setters
     */
    public function setVariableName($variableName = null)
    {
        if (null !== $variableName) {
            $this->variableName = ltrim($variableName, '$');
        }
    }
    public function getVariableName()
    {
        return $this->variableName;
    }
    /**
     * {@inheritDoc}
     */
    public function generate()
    {
        return '@var' . (!empty($this->types) ? ' ' . $this->getTypesAsString() : '') . (null !== $this->variableName ? ' $' . $this->variableName : '') . (!empty($this->description) ? ' ' . $this->description : '');
    }
}