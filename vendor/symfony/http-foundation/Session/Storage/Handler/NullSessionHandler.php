<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;

/**
 * Can be used in unit testing or in a situations where persisted sessions are not desired.
 *
 * @author Drak <drak@zikula.org>
 */
class NullSessionHandler extends AbstractSessionHandler
{
    /**
     * @return bool
     */
    public function close()
    {
        return true;
    }
    /**
     * @return bool
     */
    public function validateId($sessionId)
    {
        return true;
    }
    /**
     * {@inheritdoc}
     */
    protected function doRead($sessionId)
    {
        return '';
    }
    /**
     * @return bool
     */
    public function updateTimestamp($sessionId, $data)
    {
        return true;
    }
    /**
     * {@inheritdoc}
     */
    protected function doWrite($sessionId, $data)
    {
        return true;
    }
    /**
     * {@inheritdoc}
     */
    protected function doDestroy($sessionId)
    {
        return true;
    }
    /**
     * @return bool
     */
    public function gc($maxlifetime)
    {
        return true;
    }
}