<?php

declare(strict_types=1);

namespace DeveloperHub\ShareCart\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Config extends AbstractHelper
{
    const IS_SHARE_CART_ENABLED = 'developerhub/share_cart/share_cart_enabled';

    /**
     * @param null|int $storeId
     * @return bool
     */
    public function isShareCartEnabled($storeId = null) : bool
    {
        return $this->scopeConfig->isSetFlag(
            self::IS_SHARE_CART_ENABLED,
            ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
            $storeId
        );
    }
}
