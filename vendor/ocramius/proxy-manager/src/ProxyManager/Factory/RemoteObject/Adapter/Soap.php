<?php

namespace ProxyManager\Factory\RemoteObject\Adapter;

/**
 * Remote Object SOAP adapter
 *
 * @author Vincent Blanchon <blanchon.vincent@gmail.com>
 * @license MIT
 */
class Soap extends BaseAdapter
{
    /**
     * {@inheritDoc}
     */
    protected function getServiceName($wrappedClass, $method)
    {
        return $method;
    }
}