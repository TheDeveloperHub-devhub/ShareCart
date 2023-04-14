<?php declare(strict_types=1);

namespace DeveloperHub\ShareCart\Helper;

use Magento\Checkout\Model\SessionFactory as CheckoutSessionFactory;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Math\Random;
use Magento\Quote\Model\Quote;
use Magento\Store\Model\StoreManagerInterface;
use DeveloperHub\ShareCart\Model\Repository\ShareCartRepository;
use DeveloperHub\ShareCart\Model\ShareCartFactory as ModelFactory;

class GenerateSharingUrl extends AbstractHelper
{
    /** @var ShareCartRepository */
    private $repository;

    /** @var CheckoutSessionFactory */
    private $checkoutSessionFactory;

    /** @var Random */
    private $randomHashGenerator;

    /** @var ModelFactory */
    private $modelFactory;

    /** @var StoreManagerInterface */
    private $storeManager;

    /**
     * @param Context $context
     * @param CheckoutSessionFactory $checkoutSessionFactory
     * @param ShareCartRepository $repository
     * @param Random $randomHashGenerator
     * @param ModelFactory $modelFactory
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Context $context,
        CheckoutSessionFactory $checkoutSessionFactory,
        ShareCartRepository $repository,
        Random $randomHashGenerator,
        ModelFactory $modelFactory,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context);
        $this->checkoutSessionFactory = $checkoutSessionFactory;
        $this->repository = $repository;
        $this->randomHashGenerator = $randomHashGenerator;
        $this->modelFactory = $modelFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     */
    public function getEmailUrl() : string
    {
        return $this->storeManager->getStore()->getBaseUrl() . 'sharecart/index/addtocart/key/' . $this->getSharingUrlHash();
    }

    /**
     * @return string
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    private function getSharingUrlHash() : string
    {
        $checkoutSession = $this->checkoutSessionFactory->create();
        /** @var Quote $quote */
        $quote = $checkoutSession->getQuote();
        $model = $this->modelFactory->create();
        $modelProductData = [];
        foreach ($quote->getAllVisibleItems() as $item) {
            $sku = $item->getSku();
            $qty = $item->getQty();
            $modelProductData[$sku] = $qty;
        }
        $cartData = json_encode($modelProductData);
        $model->setProductData($cartData);
        $model->setHash($this->randomHashGenerator->getUniqueHash());
        $this->repository->save($model);
        return $model->getHash();
    }
}
