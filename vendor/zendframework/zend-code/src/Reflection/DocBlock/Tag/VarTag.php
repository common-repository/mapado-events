<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Zend\Code\Reflection\DocBlock\Tag;

class VarTag implements TagInterface, PhpDocTypedTagInterface
{
    /**
     * @var string[]
     */
    private $types = [];
    /**
     * @var string|null
     */
    private $variableName;
    /**
     * @var string|null
     */
    private $description;
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'var';
    }
    /**
     * {@inheritDoc}
     */
    public function initialize($tagDocblockLine)
    {
        $match = [];
        if (!preg_match('#^([^\\$]\\S+)?\\s*(\\$[\\S]+)?\\s*(.*)$#m', $tagDocblockLine, $match)) {
            return;
        }
        if ($match[1] !== '') {
            $this->types = explode('|', rtrim($match[1]));
        }
        if ($match[2] !== '') {
            $this->variableName = $match[2];
        }
        if ($match[3] !== '') {
            $this->description = $match[3];
        }
    }
    /**
     * {@inheritDoc}
     */
    public function getTypes()
    {
        return $this->types;
    }
    public function getVariableName()
    {
        return $this->variableName;
    }
    public function getDescription()
    {
        return $this->description;
    }
    public function __toString()
    {
        return 'DocBlock Tag [ * @' . $this->getName() . ' ]' . PHP_EOL;
    }
}