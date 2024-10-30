<?php

namespace Mapado\Sdk\Entity\Repository;

use Mapado\RestClientSdk\EntityRepository;
use Mapado\RestClientSdk\Exception\RestClientException;
class UserRepository extends EntityRepository
{
    public function me($param)
    {
        $path = '/v2/me?fields=' . $param['fields'];
        $data = $this->restClient->get($path);
        return $this->sdk->getModelHydrator()->hydrate($data, $this->entityName);
        // hydrate for an entity, hydrateList for a list
    }
    public function getListsImported($id, $param)
    {
        $path = '/v2/users/' . $id . '/user-lists?fields=' . $param['fields'];
        $data = $this->restClient->get($path);
        return $this->sdk->getModelHydrator()->hydrateList($data, $this->entityName);
        // hydrate for an entity, hydrateList for a list
    }
}