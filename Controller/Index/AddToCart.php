<?php

declare(strict_types=1);

namespace DeveloperHub\ShareCart\Controller\Index;

use Magento\Catalog\Model\ProductRepository;
use Magento\Checkout\Model\SessionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use DeveloperHub\ShareCart\Model\Repository\ShareCartRepository;

class AddToCart implements ActionInterface
{
    /** @var SessionFactory */
    private $checkoutSessionFactory;

    /** @var CartRepositoryInterface */
    private $cartRepository;

    /** @var RequestInterface */
    private $request;

    /** @var ShareCartRepository */
    private $repository;

    /** @var ProductRepository */
    private $productRepository;

    /** @var RedirectFactory */
    private $redirectFactory;

    /**
     * @param SessionFactory $checkoutSessionFactory
     * @param CartRepositoryInterface $cartRepository
     * @param RequestInterface $request
     * @param ShareCartRepository $repository
     * @param ProductRepository $productRepository
     * @param RedirectFactory $redirectFactory
     */
    public function __construct(
        SessionFactory $checkoutSessionFactory,
        CartRepositoryInterface $cartRepository,
        RequestInterface $request,
        ShareCartRepository $repository,
        ProductRepository $productRepository,
        RedirectFactory $redirectFactory
    ) {
        $this->checkoutSessionFactory = $checkoutSessionFactory;
        $this->cartRepository = $cartRepository;
        $this->request = $request;
        $this->repository = $repository;
        $this->productRepository = $productRepository;
        $this->redirectFactory = $redirectFactory;
    }

    /**
     * @return Redirect
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute() : Redirect
    {
        $redirect = $this->redirectFactory->create();
        $params = $this->request->getParams();
        $hash = $params['key'];
        $model = $this->repository->getByHash($hash);
        $productData = json_decode($model->getProductData(), true);
        $session = $this->checkoutSessionFactory->create();
        $quote = $session->getQuote();
        foreach ($productData as $sku => $qty) {
            $product = $this->productRepository->get($sku);
            $quote->addProduct($product, $qty);
        }
        $this->cartRepository->save($quote);

        return $redirect->setPath("checkout/cart/index");
    }
}
