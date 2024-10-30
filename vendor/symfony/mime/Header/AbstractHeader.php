<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\Mime\Header;

use Symfony\Component\Mime\Encoder\QpMimeHeaderEncoder;
/**
 * An abstract base MIME Header.
 *
 * @author Chris Corbyn
 */
abstract class AbstractHeader implements HeaderInterface
{
    const PHRASE_PATTERN = '(?:(?:(?:(?:(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(\\((?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])|(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21-\\x27\\x2A-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])|(?1)))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?\\)))*(?:(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(\\((?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])|(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21-\\x27\\x2A-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])|(?1)))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?\\)))|(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])))?[a-zA-Z0-9!#\\$%&\'\\*\\+\\-\\/=\\?\\^_`\\{\\}\\|~]+(?:(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(\\((?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])|(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21-\\x27\\x2A-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])|(?1)))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?\\)))*(?:(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(\\((?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])|(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21-\\x27\\x2A-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])|(?1)))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?\\)))|(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])))?)|(?:(?:(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(\\((?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])|(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21-\\x27\\x2A-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])|(?1)))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?\\)))*(?:(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(\\((?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])|(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21-\\x27\\x2A-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])|(?1)))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?\\)))|(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])))?"((?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21\\x23-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?"(?:(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(\\((?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])|(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21-\\x27\\x2A-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])|(?1)))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?\\)))*(?:(?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?(\\((?:(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])|(?:(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x19\\x7F]|[\\x21-\\x27\\x2A-\\x5B\\x5D-\\x7E])|(?:\\[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F])|(?1)))*(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])?\\)))|(?:(?:[ \\t]*(?:\\r\\n))?[ \\t])))?))+?)';
    private static $encoder;
    private $name;
    private $lineLength = 76;
    private $lang;
    private $charset = 'utf-8';
    public function __construct($name)
    {
        $this->name = $name;
    }
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }
    public function getCharset()
    {
        return $this->charset;
    }
    /**
     * Set the language used in this Header.
     *
     * For example, for US English, 'en-us'.
     */
    public function setLanguage($lang)
    {
        $this->lang = $lang;
    }
    public function getLanguage()
    {
        return $this->lang;
    }
    public function getName()
    {
        return $this->name;
    }
    public function setMaxLineLength($lineLength)
    {
        $this->lineLength = $lineLength;
    }
    public function getMaxLineLength()
    {
        return $this->lineLength;
    }
    public function toString()
    {
        return $this->tokensToString($this->toTokens());
    }
    /**
     * Produces a compliant, formatted RFC 2822 'phrase' based on the string given.
     *
     * @param string $string  as displayed
     * @param bool   $shorten the first line to make remove for header name
     */
    protected function createPhrase(HeaderInterface $header, $string, $charset, $shorten = false)
    {
        // Treat token as exactly what was given
        $phraseStr = $string;
        // If it's not valid
        if (!preg_match('/^' . self::PHRASE_PATTERN . '$/D', $phraseStr)) {
            // .. but it is just ascii text, try escaping some characters
            // and make it a quoted-string
            if (preg_match('/^[\\x00-\\x08\\x0B\\x0C\\x0E-\\x7F]*$/D', $phraseStr)) {
                foreach (['\\', '"'] as $char) {
                    $phraseStr = str_replace($char, '\\' . $char, $phraseStr);
                }
                $phraseStr = '"' . $phraseStr . '"';
            } else {
                // ... otherwise it needs encoding
                // Determine space remaining on line if first line
                if ($shorten) {
                    $usedLength = \strlen($header->getName() . ': ');
                } else {
                    $usedLength = 0;
                }
                $phraseStr = $this->encodeWords($header, $string, $usedLength);
            }
        }
        return $phraseStr;
    }
    /**
     * Encode needed word tokens within a string of input.
     */
    protected function encodeWords(HeaderInterface $header, $input, $usedLength = -1)
    {
        $value = '';
        $tokens = $this->getEncodableWordTokens($input);
        foreach ($tokens as $token) {
            // See RFC 2822, Sect 2.2 (really 2.2 ??)
            if ($this->tokenNeedsEncoding($token)) {
                // Don't encode starting WSP
                $firstChar = substr($token, 0, 1);
                switch ($firstChar) {
                    case ' ':
                    case "\t":
                        $value .= $firstChar;
                        $token = substr($token, 1);
                }
                if (-1 == $usedLength) {
                    $usedLength = \strlen($header->getName() . ': ') + \strlen($value);
                }
                $value .= $this->getTokenAsEncodedWord($token, $usedLength);
            } else {
                $value .= $token;
            }
        }
        return $value;
    }
    protected function tokenNeedsEncoding($token)
    {
        return (bool) preg_match('~[\\x00-\\x08\\x10-\\x19\\x7F-\\xFF\\r\\n]~', $token);
    }
    /**
     * Splits a string into tokens in blocks of words which can be encoded quickly.
     *
     * @return string[]
     */
    protected function getEncodableWordTokens($string)
    {
        $tokens = [];
        $encodedToken = '';
        // Split at all whitespace boundaries
        foreach (preg_split('~(?=[\\t ])~', $string) as $token) {
            if ($this->tokenNeedsEncoding($token)) {
                $encodedToken .= $token;
            } else {
                if (\strlen($encodedToken) > 0) {
                    $tokens[] = $encodedToken;
                    $encodedToken = '';
                }
                $tokens[] = $token;
            }
        }
        if (\strlen($encodedToken)) {
            $tokens[] = $encodedToken;
        }
        return $tokens;
    }
    /**
     * Get a token as an encoded word for safe insertion into headers.
     */
    protected function getTokenAsEncodedWord($token, $firstLineOffset = 0)
    {
        if (null === self::$encoder) {
            self::$encoder = new QpMimeHeaderEncoder();
        }
        // Adjust $firstLineOffset to account for space needed for syntax
        $charsetDecl = $this->charset;
        if (null !== $this->lang) {
            $charsetDecl .= '*' . $this->lang;
        }
        $encodingWrapperLength = \strlen('=?' . $charsetDecl . '?' . self::$encoder->getName() . '??=');
        if ($firstLineOffset >= 75) {
            //Does this logic need to be here?
            $firstLineOffset = 0;
        }
        $encodedTextLines = explode("\r\n", self::$encoder->encodeString($token, $this->charset, $firstLineOffset, 75 - $encodingWrapperLength));
        if ('iso-2022-jp' !== strtolower($this->charset)) {
            // special encoding for iso-2022-jp using mb_encode_mimeheader
            foreach ($encodedTextLines as $lineNum => $line) {
                $encodedTextLines[$lineNum] = '=?' . $charsetDecl . '?' . self::$encoder->getName() . '?' . $line . '?=';
            }
        }
        return implode("\r\n ", $encodedTextLines);
    }
    /**
     * Generates tokens from the given string which include CRLF as individual tokens.
     *
     * @return string[]
     */
    protected function generateTokenLines($token)
    {
        return preg_split('~(\\r\\n)~', $token, -1, PREG_SPLIT_DELIM_CAPTURE);
    }
    /**
     * Generate a list of all tokens in the final header.
     */
    protected function toTokens($string = null)
    {
        if (null === $string) {
            $string = $this->getBodyAsString();
        }
        $tokens = [];
        // Generate atoms; split at all invisible boundaries followed by WSP
        foreach (preg_split('~(?=[ \\t])~', $string) as $token) {
            $newTokens = $this->generateTokenLines($token);
            foreach ($newTokens as $newToken) {
                $tokens[] = $newToken;
            }
        }
        return $tokens;
    }
    /**
     * Takes an array of tokens which appear in the header and turns them into
     * an RFC 2822 compliant string, adding FWSP where needed.
     *
     * @param string[] $tokens
     */
    private function tokensToString(array $tokens)
    {
        $lineCount = 0;
        $headerLines = [];
        $headerLines[] = $this->name . ': ';
        $currentLine =& $headerLines[$lineCount++];
        // Build all tokens back into compliant header
        foreach ($tokens as $i => $token) {
            // Line longer than specified maximum or token was just a new line
            if ("\r\n" === $token || $i > 0 && \strlen($currentLine . $token) > $this->lineLength && 0 < \strlen($currentLine)) {
                $headerLines[] = '';
                $currentLine =& $headerLines[$lineCount++];
            }
            // Append token to the line
            if ("\r\n" !== $token) {
                $currentLine .= $token;
            }
        }
        // Implode with FWS (RFC 2822, 2.2.3)
        return implode("\r\n", $headerLines);
    }
}