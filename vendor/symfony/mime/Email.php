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
use Symfony\Component\Mime\Part\AbstractPart;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\AlternativePart;
use Symfony\Component\Mime\Part\Multipart\MixedPart;
use Symfony\Component\Mime\Part\Multipart\RelatedPart;
use Symfony\Component\Mime\Part\TextPart;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Email extends Message
{
    const PRIORITY_HIGHEST = 1;
    const PRIORITY_HIGH = 2;
    const PRIORITY_NORMAL = 3;
    const PRIORITY_LOW = 4;
    const PRIORITY_LOWEST = 5;
    const PRIORITY_MAP = [self::PRIORITY_HIGHEST => 'Highest', self::PRIORITY_HIGH => 'High', self::PRIORITY_NORMAL => 'Normal', self::PRIORITY_LOW => 'Low', self::PRIORITY_LOWEST => 'Lowest'];
    private $text;
    private $textCharset;
    private $html;
    private $htmlCharset;
    private $attachments = [];
    /**
     * @return $this
     */
    public function subject($subject)
    {
        return $this->setHeaderBody('Text', 'Subject', $subject);
    }
    public function getSubject()
    {
        return $this->getHeaders()->getHeaderBody('Subject');
    }
    /**
     * @return $this
     */
    public function date(\DateTimeInterface $dateTime)
    {
        return $this->setHeaderBody('Date', 'Date', $dateTime);
    }
    public function getDate()
    {
        return $this->getHeaders()->getHeaderBody('Date');
    }
    /**
     * @param Address|string $address
     *
     * @return $this
     */
    public function returnPath($address)
    {
        return $this->setHeaderBody('Path', 'Return-Path', Address::create($address));
    }
    public function getReturnPath()
    {
        return $this->getHeaders()->getHeaderBody('Return-Path');
    }
    /**
     * @param Address|string $address
     *
     * @return $this
     */
    public function sender($address)
    {
        return $this->setHeaderBody('Mailbox', 'Sender', Address::create($address));
    }
    public function getSender()
    {
        return $this->getHeaders()->getHeaderBody('Sender');
    }
    /**
     * @param Address|string ...$addresses
     *
     * @return $this
     */
    public function addFrom(...$addresses)
    {
        return $this->addListAddressHeaderBody('From', $addresses);
    }
    /**
     * @param Address|string ...$addresses
     *
     * @return $this
     */
    public function from(...$addresses)
    {
        return $this->setListAddressHeaderBody('From', $addresses);
    }
    /**
     * @return Address[]
     */
    public function getFrom()
    {
        return $this->getHeaders()->getHeaderBody('From') ?: [];
    }
    /**
     * @param Address|string ...$addresses
     *
     * @return $this
     */
    public function addReplyTo(...$addresses)
    {
        return $this->addListAddressHeaderBody('Reply-To', $addresses);
    }
    /**
     * @param Address|string ...$addresses
     *
     * @return $this
     */
    public function replyTo(...$addresses)
    {
        return $this->setListAddressHeaderBody('Reply-To', $addresses);
    }
    /**
     * @return Address[]
     */
    public function getReplyTo()
    {
        return $this->getHeaders()->getHeaderBody('Reply-To') ?: [];
    }
    /**
     * @param Address|string ...$addresses
     *
     * @return $this
     */
    public function addTo(...$addresses)
    {
        return $this->addListAddressHeaderBody('To', $addresses);
    }
    /**
     * @param Address|string ...$addresses
     *
     * @return $this
     */
    public function to(...$addresses)
    {
        return $this->setListAddressHeaderBody('To', $addresses);
    }
    /**
     * @return Address[]
     */
    public function getTo()
    {
        return $this->getHeaders()->getHeaderBody('To') ?: [];
    }
    /**
     * @param Address|string ...$addresses
     *
     * @return $this
     */
    public function addCc(...$addresses)
    {
        return $this->addListAddressHeaderBody('Cc', $addresses);
    }
    /**
     * @param Address|string ...$addresses
     *
     * @return $this
     */
    public function cc(...$addresses)
    {
        return $this->setListAddressHeaderBody('Cc', $addresses);
    }
    /**
     * @return Address[]
     */
    public function getCc()
    {
        return $this->getHeaders()->getHeaderBody('Cc') ?: [];
    }
    /**
     * @param Address|string ...$addresses
     *
     * @return $this
     */
    public function addBcc(...$addresses)
    {
        return $this->addListAddressHeaderBody('Bcc', $addresses);
    }
    /**
     * @param Address|string ...$addresses
     *
     * @return $this
     */
    public function bcc(...$addresses)
    {
        return $this->setListAddressHeaderBody('Bcc', $addresses);
    }
    /**
     * @return Address[]
     */
    public function getBcc()
    {
        return $this->getHeaders()->getHeaderBody('Bcc') ?: [];
    }
    /**
     * Sets the priority of this message.
     *
     * The value is an integer where 1 is the highest priority and 5 is the lowest.
     *
     * @return $this
     */
    public function priority($priority)
    {
        if ($priority > 5) {
            $priority = 5;
        } elseif ($priority < 1) {
            $priority = 1;
        }
        return $this->setHeaderBody('Text', 'X-Priority', sprintf('%d (%s)', $priority, self::PRIORITY_MAP[$priority]));
    }
    /**
     * Get the priority of this message.
     *
     * The returned value is an integer where 1 is the highest priority and 5
     * is the lowest.
     */
    public function getPriority()
    {
        list($priority) = sscanf($this->getHeaders()->getHeaderBody('X-Priority'), '%[1-5]');
        return isset($priority) ? $priority : 3;
    }
    /**
     * @param resource|string $body
     *
     * @return $this
     */
    public function text($body, $charset = 'utf-8')
    {
        $this->text = $body;
        $this->textCharset = $charset;
        return $this;
    }
    /**
     * @return resource|string|null
     */
    public function getTextBody()
    {
        return $this->text;
    }
    public function getTextCharset()
    {
        return $this->textCharset;
    }
    /**
     * @param resource|string|null $body
     *
     * @return $this
     */
    public function html($body, $charset = 'utf-8')
    {
        $this->html = $body;
        $this->htmlCharset = $charset;
        return $this;
    }
    /**
     * @return resource|string|null
     */
    public function getHtmlBody()
    {
        return $this->html;
    }
    public function getHtmlCharset()
    {
        return $this->htmlCharset;
    }
    /**
     * @param resource|string $body
     *
     * @return $this
     */
    public function attach($body, $name = null, $contentType = null)
    {
        $this->attachments[] = ['body' => $body, 'name' => $name, 'content-type' => $contentType, 'inline' => false];
        return $this;
    }
    /**
     * @return $this
     */
    public function attachFromPath($path, $name = null, $contentType = null)
    {
        $this->attachments[] = ['path' => $path, 'name' => $name, 'content-type' => $contentType, 'inline' => false];
        return $this;
    }
    /**
     * @param resource|string $body
     *
     * @return $this
     */
    public function embed($body, $name = null, $contentType = null)
    {
        $this->attachments[] = ['body' => $body, 'name' => $name, 'content-type' => $contentType, 'inline' => true];
        return $this;
    }
    /**
     * @return $this
     */
    public function embedFromPath($path, $name = null, $contentType = null)
    {
        $this->attachments[] = ['path' => $path, 'name' => $name, 'content-type' => $contentType, 'inline' => true];
        return $this;
    }
    /**
     * @return $this
     */
    public function attachPart(DataPart $part)
    {
        $this->attachments[] = ['part' => $part];
        return $this;
    }
    /**
     * @return DataPart[]
     */
    public function getAttachments()
    {
        $parts = [];
        foreach ($this->attachments as $attachment) {
            $parts[] = $this->createDataPart($attachment);
        }
        return $parts;
    }
    public function getBody()
    {
        if (null !== ($body = parent::getBody())) {
            return $body;
        }
        return $this->generateBody();
    }
    public function ensureValidity()
    {
        if (null === $this->text && null === $this->html && !$this->attachments) {
            throw new LogicException('A message must have a text or an HTML part or attachments.');
        }
        parent::ensureValidity();
    }
    /**
     * Generates an AbstractPart based on the raw body of a message.
     *
     * The most "complex" part generated by this method is when there is text and HTML bodies
     * with related images for the HTML part and some attachments:
     *
     * multipart/mixed
     *         |
     *         |------------> multipart/related
     *         |                      |
     *         |                      |------------> multipart/alternative
     *         |                      |                      |
     *         |                      |                       ------------> text/plain (with content)
     *         |                      |                      |
     *         |                      |                       ------------> text/html (with content)
     *         |                      |
     *         |                       ------------> image/png (with content)
     *         |
     *          ------------> application/pdf (with content)
     */
    private function generateBody()
    {
        $this->ensureValidity();
        [$htmlPart, $attachmentParts, $inlineParts] = $this->prepareParts();
        $part = null === $this->text ? null : new TextPart($this->text, $this->textCharset);
        if (null !== $htmlPart) {
            if (null !== $part) {
                $part = new AlternativePart($part, $htmlPart);
            } else {
                $part = $htmlPart;
            }
        }
        if ($inlineParts) {
            $part = new RelatedPart($part, ...$inlineParts);
        }
        if ($attachmentParts) {
            if ($part) {
                $part = new MixedPart($part, ...$attachmentParts);
            } else {
                $part = new MixedPart(...$attachmentParts);
            }
        }
        return $part;
    }
    private function prepareParts()
    {
        $names = [];
        $htmlPart = null;
        $html = $this->html;
        if (null !== $this->html) {
            if (\is_resource($html)) {
                if (isset(stream_get_meta_data($html)['seekable']) ? stream_get_meta_data($html)['seekable'] : false) {
                    rewind($html);
                }
                $html = stream_get_contents($html);
            }
            $htmlPart = new TextPart($html, $this->htmlCharset, 'html');
            preg_match_all('(<img\\s+[^>]*src\\s*=\\s*(?:([\'"])cid:([^"]+)\\1|cid:([^>\\s]+)))i', $html, $names);
            $names = array_filter(array_unique(array_merge($names[2], $names[3])));
        }
        $attachmentParts = $inlineParts = [];
        foreach ($this->attachments as $attachment) {
            foreach ($names as $name) {
                if (isset($attachment['part'])) {
                    continue;
                }
                if ($name !== $attachment['name']) {
                    continue;
                }
                if (isset($inlineParts[$name])) {
                    continue 2;
                }
                $attachment['inline'] = true;
                $inlineParts[$name] = $part = $this->createDataPart($attachment);
                $html = str_replace('cid:' . $name, 'cid:' . $part->getContentId(), $html);
                continue 2;
            }
            $attachmentParts[] = $this->createDataPart($attachment);
        }
        if (null !== $htmlPart) {
            $htmlPart = new TextPart($html, $this->htmlCharset, 'html');
        }
        return [$htmlPart, $attachmentParts, array_values($inlineParts)];
    }
    private function createDataPart(array $attachment)
    {
        if (isset($attachment['part'])) {
            return $attachment['part'];
        }
        if (isset($attachment['body'])) {
            $part = new DataPart($attachment['body'], isset($attachment['name']) ? $attachment['name'] : null, isset($attachment['content-type']) ? $attachment['content-type'] : null);
        } else {
            $part = DataPart::fromPath(isset($attachment['path']) ? $attachment['path'] : '', isset($attachment['name']) ? $attachment['name'] : null, isset($attachment['content-type']) ? $attachment['content-type'] : null);
        }
        if ($attachment['inline']) {
            $part->asInline();
        }
        return $part;
    }
    /**
     * @return $this
     */
    private function setHeaderBody($type, $name, $body)
    {
        $this->getHeaders()->setHeaderBody($type, $name, $body);
        return $this;
    }
    private function addListAddressHeaderBody($name, array $addresses)
    {
        if (!($header = $this->getHeaders()->get($name))) {
            return $this->setListAddressHeaderBody($name, $addresses);
        }
        $header->addAddresses(Address::createArray($addresses));
        return $this;
    }
    private function setListAddressHeaderBody($name, array $addresses)
    {
        $addresses = Address::createArray($addresses);
        $headers = $this->getHeaders();
        if ($header = $headers->get($name)) {
            $header->setAddresses($addresses);
        } else {
            $headers->addMailboxListHeader($name, $addresses);
        }
        return $this;
    }
    /**
     * @internal
     */
    public function __serialize()
    {
        if (\is_resource($this->text)) {
            if (isset(stream_get_meta_data($this->text)['seekable']) ? stream_get_meta_data($this->text)['seekable'] : false) {
                rewind($this->text);
            }
            $this->text = stream_get_contents($this->text);
        }
        if (\is_resource($this->html)) {
            if (isset(stream_get_meta_data($this->html)['seekable']) ? stream_get_meta_data($this->html)['seekable'] : false) {
                rewind($this->html);
            }
            $this->html = stream_get_contents($this->html);
        }
        foreach ($this->attachments as $i => $attachment) {
            if (isset($attachment['body']) && \is_resource($attachment['body'])) {
                if (isset(stream_get_meta_data($attachment['body'])['seekable']) ? stream_get_meta_data($attachment['body'])['seekable'] : false) {
                    rewind($attachment['body']);
                }
                $this->attachments[$i]['body'] = stream_get_contents($attachment['body']);
            }
        }
        return [$this->text, $this->textCharset, $this->html, $this->htmlCharset, $this->attachments, parent::__serialize()];
    }
    /**
     * @internal
     */
    public function __unserialize(array $data)
    {
        [$this->text, $this->textCharset, $this->html, $this->htmlCharset, $this->attachments, $parentData] = $data;
        parent::__unserialize($parentData);
    }
}