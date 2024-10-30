<?php

namespace Mapado\RestClientSdk;

use Mapado\RestClientSdk\Exception\SdkClientNotFoundException;
class SdkClientRegistry
{
    /** var array<string, SdkClient> $sdkClientList */
    private $sdkClientList;
    /**
     * @param array<string, SdkClient> $sdkClientList
     */
    public function __construct(array $sdkClientList)
    {
        $this->sdkClientList = $sdkClientList;
    }
    public function getSdkClient($name)
    {
        $client = isset($this->sdkClientList[$name]) ? $this->sdkClientList[$name] : null;
        if (!$client) {
            throw new SdkClientNotFoundException('Sdk client not found for name ' . $name);
        }
        return $client;
    }
    /**
     * @return array<SdkClient>
     */
    public function getSdkClientList()
    {
        return $this->sdkClientList;
    }
    public function getSdkClientForClass($entityClassname)
    {
        foreach ($this->sdkClientList as $sdkClient) {
            if ($sdkClient->getMapping()->hasClassMetadata($entityClassname)) {
                return $sdkClient;
            }
        }
        throw new SdkClientNotFoundException('Sdk client not found for entity class ' . $entityClassname);
    }
}