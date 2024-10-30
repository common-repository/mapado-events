<?php

namespace ProxyManager\Factory\RemoteObject\Adapter;

/**
 * Remote Object JSON RPC adapter
 *
 * @author Vincent Blanchon <blanchon.vincent@gmail.com>
 * @license MIT
 */
class JsonRpc extends BaseAdapter
{
    /**
     * {@inheritDoc}
     */
    protected function getServiceName($wrappedClass, $method)
    {
        return $wrappedClass . '.' . $method;
    }
}