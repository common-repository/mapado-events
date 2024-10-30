<?php

namespace Mapado\RestClientSdk\Exception;

use Exception;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
/**
 * Class RestException
 *
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class RestException extends \RuntimeException
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var array
     */
    private $params;
    /**
     * @var ResponseInterface|null
     */
    private $response;
    /**
     * @var RequestInterface|null
     */
    private $request;
    public function __construct($message, $path, array $params = [], $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->path = $path;
        $this->params = $params;
        if ($previous instanceof RequestException) {
            $this->response = $previous->getResponse();
            $this->request = $previous->getRequest();
        }
    }
    public function getPath()
    {
        return $this->path;
    }
    public function getParams()
    {
        return $this->params;
    }
    public function getResponse()
    {
        return $this->response;
    }
    public function getRequest()
    {
        return $this->request;
    }
}