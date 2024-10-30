<?php

namespace Mapado\RestClientSdk\Collection;

/**
 * Class HydraPaginatedCollection
 *
 * @author Florent Clerc <florent.clerc@mapado.com>
 */
class HydraPaginatedCollection extends Collection
{
    /**
     * Returns first page URI.
     */
    public function getFirstPage()
    {
        return $this->getExtraProperty('hydra:firstPage');
    }
    /**
     * Returns last page URI.
     */
    public function getLastPage()
    {
        return $this->getExtraProperty('hydra:lastPage');
    }
    /**
     * Returns next page URI.
     */
    public function getNextPage()
    {
        return $this->getExtraProperty('hydra:nextPage');
    }
    /**
     * Returns total item count.
     */
    public function getTotalItems()
    {
        return !empty($this->getExtraProperty('hydra:totalItems')) ? $this->getExtraProperty('hydra:totalItems') : 0;
    }
}