<?php

declare(strict_types=1);

namespace DeveloperHub\ShareCart\Api\Data;

use Magento\Framework\Data\SearchResultInterface as BaseSearchResultInterface;

interface SearchResultInterface extends BaseSearchResultInterface
{
    /**
     * Retrieve collection items
     *
     * @return ShareCartInterface[]
     */
    public function getItems();

    /**
     * @params ShareCartInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
