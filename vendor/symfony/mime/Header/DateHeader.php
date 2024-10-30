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

/**
 * A Date MIME Header.
 *
 * @author Chris Corbyn
 */
final class DateHeader extends AbstractHeader
{
    private $dateTime;
    public function __construct($name, \DateTimeInterface $date)
    {
        parent::__construct($name);
        $this->setDateTime($date);
    }
    /**
     * @param \DateTimeInterface $body
     */
    public function setBody($body)
    {
        $this->setDateTime($body);
    }
    public function getBody()
    {
        return $this->getDateTime();
    }
    public function getDateTime()
    {
        return $this->dateTime;
    }
    /**
     * Set the date-time of the Date in this Header.
     *
     * If a DateTime instance is provided, it is converted to DateTimeImmutable.
     */
    public function setDateTime(\DateTimeInterface $dateTime)
    {
        if ($dateTime instanceof \DateTime) {
            $immutable = new \DateTimeImmutable('@' . $dateTime->getTimestamp());
            $dateTime = $immutable->setTimezone($dateTime->getTimezone());
        }
        $this->dateTime = $dateTime;
    }
    public function getBodyAsString()
    {
        return $this->dateTime->format(\DateTime::RFC2822);
    }
}