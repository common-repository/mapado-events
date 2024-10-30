<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\Mime\Encoder;

/**
 * @author Chris Corbyn
 */
final class Base64MimeHeaderEncoder extends Base64Encoder implements MimeHeaderEncoderInterface
{
    public function getName()
    {
        return 'B';
    }
    /**
     * Takes an unencoded string and produces a Base64 encoded string from it.
     *
     * If the charset is iso-2022-jp, it uses mb_encode_mimeheader instead of
     * default encodeString, otherwise pass to the parent method.
     */
    public function encodeString($string, $charset = 'utf-8', $firstLineOffset = 0, $maxLineLength = 0)
    {
        if ('iso-2022-jp' === strtolower($charset)) {
            $old = mb_internal_encoding();
            mb_internal_encoding('utf-8');
            $newstring = mb_encode_mimeheader($string, 'iso-2022-jp', $this->getName(), "\r\n");
            mb_internal_encoding($old);
            return $newstring;
        }
        return parent::encodeString($string, $charset, $firstLineOffset, $maxLineLength);
    }
}