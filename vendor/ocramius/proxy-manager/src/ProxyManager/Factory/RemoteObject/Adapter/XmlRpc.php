<?php

namespace ProxyManager\Factory\RemoteObject\Adapter;

/**
 * Remote Object XML RPC adapter
 *
 * @author Vincent Blanchon <blanchon.vincent@gmail.com>
 * @license MIT
 */
class XmlRpc extends BaseAdapter
{
    /**
     * {@inheritDoc}
     */
    protected function getServiceName($wrappedClass, $method)
    {
        return $wrappedClass . '.' . $method;
    }
}