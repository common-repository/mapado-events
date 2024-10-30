<?php

namespace Mapado\RestClientSdk;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\TransferException;
use Mapado\RestClientSdk\Exception\RestClientException;
use Mapado\RestClientSdk\Exception\RestException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
/**
 * Class RestClient
 *
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
class RestClient
{
    /**
     * @var ClientInterface
     */
    private $httpClient;
    /**
     * @var ?string
     */
    private $baseUrl;
    /**
     * @var bool
     */
    private $logHistory;
    /**
     * @var array
     */
    private $requestHistory;
    /**
     * @var ?Request
     */
    private $currentRequest;
    public function __construct(ClientInterface $httpClient, $baseUrl = null)
    {
        $this->httpClient = $httpClient;
        $this->baseUrl = null !== $baseUrl && '/' === mb_substr($baseUrl, -1) ? mb_substr($baseUrl, 0, -1) : $baseUrl;
        $this->logHistory = false;
        $this->requestHistory = [];
    }
    public function isHistoryLogged()
    {
        return $this->logHistory;
    }
    public function setCurrentRequest(Request $currentRequest)
    {
        $this->currentRequest = $currentRequest;
        return $this;
    }
    public function setLogHistory($logHistory)
    {
        $this->logHistory = $logHistory;
        return $this;
    }
    public function getRequestHistory()
    {
        return $this->requestHistory;
    }
    /**
     * get a path
     *
     * @return array|ResponseInterface|null
     *
     * @throws RestException
     */
    public function get($path, array $parameters = [])
    {
        $requestUrl = $this->baseUrl . $path;
        try {
            return $this->executeRequest('GET', $requestUrl, $parameters);
        } catch (ClientException $e) {
            $response = $e->getResponse();
            if (null !== $response && 404 === $response->getStatusCode()) {
                return null;
            }
            throw new RestClientException('Error while getting resource', $path, [], 7, $e);
        } catch (TransferException $e) {
            throw new RestException('Error while getting resource', $path, [], 1, $e);
        }
    }
    /**
     * @throws RestException
     */
    public function delete($path)
    {
        try {
            $this->executeRequest('DELETE', $this->baseUrl . $path);
        } catch (ClientException $e) {
            return;
        } catch (TransferException $e) {
            throw new RestException('Error while deleting resource', $path, [], 2, $e);
        }
    }
    /**
     * @return array|ResponseInterface
     *
     * @throws RestClientException
     * @throws RestException
     */
    public function post($path, array $data, array $parameters = [])
    {
        $parameters['json'] = $data;
        try {
            return $this->executeRequest('POST', $this->baseUrl . $path, $parameters);
        } catch (ClientException $e) {
            throw new RestClientException('Cannot create resource', $path, [], 3, $e);
        } catch (TransferException $e) {
            throw new RestException('Error while posting resource', $path, [], 4, $e);
        }
    }
    /**
     * @return array|ResponseInterface
     *
     * @throws RestClientException
     * @throws RestException
     */
    public function put($path, array $data, array $parameters = [])
    {
        $parameters['json'] = $data;
        try {
            return $this->executeRequest('PUT', $this->baseUrl . $path, $parameters);
        } catch (ClientException $e) {
            throw new RestClientException('Cannot update resource', $path, [], 5, $e);
        } catch (TransferException $e) {
            throw new RestException('Error while puting resource', $path, [], 6, $e);
        }
    }
    /**
     * Merge default parameters.
     */
    protected function mergeDefaultParameters(array $parameters)
    {
        $request = $this->getCurrentRequest();
        $defaultParameters = ['version' => '1.0'];
        if (null !== $request) {
            $defaultParameters['headers'] = ['Referer' => $request->getUri()];
        }
        $out = array_replace_recursive($defaultParameters, $parameters);
        if (null === $out) {
            throw new \RuntimeException(sprintf('Error while calling array_replace_recursive in %s. This should not happen.', __METHOD__));
        }
        return $out;
    }
    protected function getCurrentRequest()
    {
        if ('cli' === \PHP_SAPI) {
            // we are in cli mode, do not bother to get request
            return null;
        }
        if (!$this->currentRequest) {
            $this->currentRequest = Request::createFromGlobals();
        }
        return $this->currentRequest;
    }
    /**
     * Executes request.
     *
     * @return ResponseInterface|array
     *
     * @throws TransferException
     */
    private function executeRequest($method, $url, array $parameters = [])
    {
        $parameters = $this->mergeDefaultParameters($parameters);
        $startTime = null;
        if ($this->isHistoryLogged()) {
            $startTime = microtime(true);
        }
        try {
            $response = $this->httpClient->request($method, $url, $parameters);
            $this->logRequest($startTime, $method, $url, $parameters, $response);
        } catch (RequestException $e) {
            $this->logRequest($startTime, $method, $url, $parameters, $e->getResponse());
            throw $e;
        } catch (TransferException $e) {
            $this->logRequest($startTime, $method, $url, $parameters);
            throw $e;
        }
        $headers = $response->getHeaders();
        $jsonContentTypeList = ['application/ld+json', 'application/json'];
        $requestIsJson = false;
        $responseContentType = isset($headers['Content-Type']) ? $headers['Content-Type'] : (isset($headers['content-type']) ? $headers['content-type'] : null);
        if ($responseContentType) {
            foreach ($jsonContentTypeList as $contentType) {
                if (false !== mb_stripos($responseContentType[0], $contentType)) {
                    $requestIsJson = true;
                    break;
                }
            }
        }
        if ($requestIsJson) {
            return json_decode((string) $response->getBody(), true);
        } else {
            return $response;
        }
    }
    private function logRequest($startTime = null, $method, $url, array $parameters, ResponseInterface $response = null)
    {
        if ($this->isHistoryLogged()) {
            $queryTime = microtime(true) - $startTime;
            $this->requestHistory[] = ['method' => $method, 'url' => $url, 'parameters' => $parameters, 'response' => $response, 'responseBody' => $response ? json_decode((string) $response->getBody(), true) : null, 'queryTime' => $queryTime, 'backtrace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)];
        }
    }
}