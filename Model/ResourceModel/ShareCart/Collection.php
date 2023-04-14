<?php

declare(strict_types=1);

namespace DeveloperHub\ShareCart\Model\ResourceModel\ShareCart;

use Magento\Catalog\Model\ResourceModel\AbstractCollection;
use DeveloperHub\ShareCart\Api\Data\ShareCartInterface;
use DeveloperHub\ShareCart\Model\ResourceModel\ShareCart as ResourceModel;
use DeveloperHub\ShareCart\Model\ShareCart as Model;

class Collection extends AbstractCollection
{
    protected $_idFieldName = ShareCartInterface::ID;

    /**
     * @inheirtDoc
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(Model::class, ResourceModel::class);
    }
}
