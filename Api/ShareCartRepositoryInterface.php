<?php

declare(strict_types=1);

namespace DeveloperHub\ShareCart\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use DeveloperHub\ShareCart\Api\Data\SearchResultInterface;
use DeveloperHub\ShareCart\Api\Data\ShareCartInterface;

interface ShareCartRepositoryInterface
{
    /**
     * @param ShareCartInterface $cart
     * @return ShareCartInterface
     * @throws CouldNotSaveException
     */
    public function save(ShareCartInterface $cart);

    /**
     * @param $id
     * @return ShareCartInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultInterface
     * @throws NoSuchEntityException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param int $id
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $id);

    /**
     * @param int $id
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ShareCartInterface $cart);
}
