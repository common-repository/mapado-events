<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\Mime\Part;

use Symfony\Component\Mime\Header\Headers;
/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class SMimePart extends AbstractPart
{
    private $body;
    private $type;
    private $subtype;
    private $parameters;
    /**
     * @param iterable|string $body
     */
    public function __construct($body, $type, $subtype, array $parameters)
    {
        parent::__construct();
        if (!\is_string($body) && !is_iterable($body)) {
            throw new \TypeError(sprintf('The body of "%s" must be a string or a iterable (got "%s").', self::class, \is_object($body) ? \get_class($body) : \gettype($body)));
        }
        $this->body = $body;
        $this->type = $type;
        $this->subtype = $subtype;
        $this->parameters = $parameters;
    }
    public function getMediaType()
    {
        return $this->type;
    }
    public function getMediaSubtype()
    {
        return $this->subtype;
    }
    public function bodyToString()
    {
        if (\is_string($this->body)) {
            return $this->body;
        }
        $body = '';
        foreach ($this->body as $chunk) {
            $body .= $chunk;
        }
        $this->body = $body;
        return $body;
    }
    public function bodyToIterable()
    {
        if (\is_string($this->body)) {
            (yield $this->body);
            return;
        }
        $body = '';
        foreach ($this->body as $chunk) {
            $body .= $chunk;
            (yield $chunk);
        }
        $this->body = $body;
    }
    public function getPreparedHeaders()
    {
        $headers = clone parent::getHeaders();
        $headers->setHeaderBody('Parameterized', 'Content-Type', $this->getMediaType() . '/' . $this->getMediaSubtype());
        foreach ($this->parameters as $name => $value) {
            $headers->setHeaderParameter('Content-Type', $name, $value);
        }
        return $headers;
    }
    public function __sleep()
    {
        // convert iterables to strings for serialization
        if (is_iterable($this->body)) {
            $this->body = $this->bodyToString();
        }
        $this->_headers = $this->getHeaders();
        return ['_headers', 'body', 'type', 'subtype', 'parameters'];
    }
    public function __wakeup()
    {
        $r = new \ReflectionProperty(AbstractPart::class, 'headers');
        $r->setAccessible(true);
        $r->setValue($this, $this->_headers);
        unset($this->_headers);
    }
}