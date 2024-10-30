<?php

namespace Zend\Code;

use Zend\Code\Exception\InvalidArgumentException;
class DeclareStatement
{
    const TICKS = 'ticks';
    const STRICT_TYPES = 'strict_types';
    const ENCODING = 'encoding';
    const ALLOWED = [self::TICKS => 'integer', self::STRICT_TYPES => 'integer', self::ENCODING => 'string'];
    /**
     * @var string
     */
    protected $directive;
    /**
     * @var int|string
     */
    protected $value;
    private function __construct($directive, $value)
    {
        $this->directive = $directive;
        $this->value = $value;
    }
    /**
     * @return string
     */
    public function getDirective()
    {
        return $this->directive;
    }
    /**
     * @return int|string
     */
    public function getValue()
    {
        return $this->value;
    }
    /**
     * @param int $value
     * @return self
     */
    public static function ticks($value)
    {
        return new self(self::TICKS, $value);
    }
    /**
     * @param int $value
     * @return self
     */
    public static function strictTypes($value)
    {
        return new self(self::STRICT_TYPES, $value);
    }
    /**
     * @param string $value
     * @return self
     */
    public static function encoding($value)
    {
        return new self(self::ENCODING, $value);
    }
    public static function fromArray(array $config)
    {
        $directive = key($config);
        $value = $config[$directive];
        if (!isset(self::ALLOWED[$directive])) {
            throw new InvalidArgumentException(sprintf('Declare directive must be one of: %s.', implode(', ', array_keys(self::ALLOWED))));
        }
        if (gettype($value) !== self::ALLOWED[$directive]) {
            throw new InvalidArgumentException(sprintf('Declare value invalid. Expected %s, got %s.', self::ALLOWED[$directive], gettype($value)));
        }
        $method = str_replace('_', '', lcfirst(ucwords($directive, '_')));
        return self::$method($value);
    }
    /**
     * @return string
     */
    public function getStatement()
    {
        $value = is_string($this->value) ? '\'' . $this->value . '\'' : $this->value;
        return sprintf('declare(%s=%s);', $this->directive, $value);
    }
}