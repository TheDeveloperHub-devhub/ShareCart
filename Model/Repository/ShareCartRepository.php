<?php declare(strict_types=1);

namespace DeveloperHub\ShareCart\Model\Repository;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use DeveloperHub\ShareCart\Api\Data\SearchResultInterface;
use DeveloperHub\ShareCart\Api\Data\SearchResultInterfaceFactory;
use DeveloperHub\ShareCart\Api\Data\ShareCartInterface;
use DeveloperHub\ShareCart\Api\ShareCartRepositoryInterface;
use DeveloperHub\ShareCart\Model\ResourceModel\ShareCart as ResourceModel;
use DeveloperHub\ShareCart\Model\ResourceModel\ShareCart\Collection;
use DeveloperHub\ShareCart\Model\ResourceModel\ShareCart\CollectionFactory;
use DeveloperHub\ShareCart\Model\ShareCart as Model;
use DeveloperHub\ShareCart\Model\ShareCartFactory as ModelFactory;

class ShareCartRepository implements ShareCartRepositoryInterface
{
    /** @var array */
    private $configuration = [];

    /** @var ResourceModel */
    private $resourceModel;

    /** @var ModelFactory */
    private $modelFactory;

    /** @var CollectionFactory */
    private $collectionFactory;

    /** @var SearchResultInterfaceFactory */
    private $searchResultInterfaceFactory;

    /** @var CollectionProcessorInterface */
    private $collectionProcessor;

    /**
     * @param ModelFactory $modelFactory
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     * @param SearchResultInterfaceFactory $searchResultInterfaceFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ModelFactory $modelFactory,
        ResourceModel $resourceModel,
        CollectionFactory $collectionFactory,
        SearchResultInterfaceFactory $searchResultInterfaceFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->modelFactory = $modelFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
        $this->searchResultInterfaceFactory = $searchResultInterfaceFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @param ShareCartInterface $cart
     * @return ShareCartInterface
     * @throws CouldNotSaveException
     */
    public function save(ShareCartInterface $cart)
    {
        try {
            if ($cart->getId()) {
                $cart = $this->getById($cart->getId())
                    ->addData($cart->getData());
            }
            $this->resourceModel->save($cart);
            unset($this->configuration[$cart->getId()]);
        } catch (\Exception $exception) {
            if ($cart->getId()) {
                throw new CouldNotSaveException(
                    __(
                        'Unable to save configuration with ID %1. Error: %2',
                        [$cart->getId(), $exception->getMessage()]
                    )
                );
            }
            throw new CouldNotSaveException(
                __('Unable to save new configuration. Error: %1', $exception->getMessage())
            );
        }
        return $cart;
    }

    /**
     * @param $id
     * @return mixed|ShareCartInterface|Model
     * @throws NoSuchEntityException
     */
    public function getById($id)
    {
        if (!isset($this->configuration[$id])) {
            /** @var Model $model */
            $model = $this->modelFactory->create();
            $this->resourceModel->load($model, $id);
            if (!$model->getId()) {
                throw new NoSuchEntityException(__('Configuration with specified ID "%1" not found.', $id));
            }
            $this->configuration[$id] = $model;
        }
        return $this->configuration[$id];
    }

    /**
     * @param $hash
     * @return Model
     * @throws NoSuchEntityException
     */
    public function getByHash($hash)
    {
        $model = $this->modelFactory->create();
        $this->resourceModel->load($model, $hash, "hash");
        if (!$model->getId()) {
            throw new NoSuchEntityException(__('Configuration with specified ID "%1" not found.', $hash));
        }
        return $model;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return SearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        /** @var SearchResultInterface $searchResult */
        $searchResult = $this->searchResultInterfaceFactory->create();
        $searchResult->setSearchCriteria($searchCriteria);
        $searchResult->setItems($collection->getItems());
        $searchResult->setTotalCount($collection->getSize());
        return $searchResult;
    }

    /**
     * @param int $id
     * @return bool|void
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById(int $id)
    {
        $model = $this->getById($id);
        $this->delete($model);
        unset($this->configuration[$id]);
    }

    /**
     * @param ShareCartInterface $cart
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ShareCartInterface $cart)
    {
        try {
            $this->resourceModel->delete($cart);
            unset($this->configuration[$cart->getId()]);
        } catch (\Exception $exception) {
            if ($cart->getId()) {
                throw new CouldNotDeleteException(
                    __(
                        'Unable to remove configuration with ID %1. Error: %2',
                        [$cart->getId(), $exception->getMessage()]
                    )
                );
            }
            throw new CouldNotDeleteException(
                __(
                    'Unable to remove configuration. Error: %1',
                    $exception->getMessage()
                )
            );
        }
        return true;
    }
}
