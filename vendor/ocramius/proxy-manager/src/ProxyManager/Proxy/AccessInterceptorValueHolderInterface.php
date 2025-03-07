<?php

namespace ProxyManager\Proxy;

/**
 * Aggregates AccessInterceptor and ValueHolderInterface, mostly for return type hinting
 *
 * @author Marco Pivetta <ocramius@gmail.com>
 * @license MIT
 */
interface AccessInterceptorValueHolderInterface extends AccessInterceptorInterface, ValueHolderInterface
{
}