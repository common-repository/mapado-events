<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\Mime;

use Symfony\Component\Mime\Exception\LogicException;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class RawMessage implements \Serializable
{
    private $message;
    /**
     * @param iterable|string $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }
    public function toString()
    {
        if (\is_string($this->message)) {
            return $this->message;
        }
        return $this->message = implode('', iterator_to_array($this->message, false));
    }
    public function toIterable()
    {
        if (\is_string($this->message)) {
            (yield $this->message);
            return;
        }
        $message = '';
        foreach ($this->message as $chunk) {
            $message .= $chunk;
            (yield $chunk);
        }
        $this->message = $message;
    }
    /**
     * @throws LogicException if the message is not valid
     */
    public function ensureValidity()
    {
    }
    /**
     * @internal
     */
    public final function serialize()
    {
        return serialize($this->__serialize());
    }
    /**
     * @internal
     */
    public final function unserialize($serialized)
    {
        $this->__unserialize(unserialize($serialized));
    }
    public function __serialize()
    {
        return [$this->message];
    }
    public function __unserialize(array $data)
    {
        [$this->message] = $data;
    }
}