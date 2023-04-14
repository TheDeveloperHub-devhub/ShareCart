<?php

declare(strict_types=1);

namespace DeveloperHub\ShareCart\Controller\Index;

use Exception;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use DeveloperHub\ShareCart\Helper\GenerateSharingUrl;

class SendEmail extends AbstractHelper implements ActionInterface
{
    /** @var TransportBuilder * */
    private $transportBuilder;

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var StateInterface * */
    private $inlineTranslation;

    /** @var ResultFactory */
    private $resultFactory;

    /** @var GenerateSharingUrl */
    private $generateSharingUrl;

    /** @var MessageManagerInterface */
    private $messageManager;

    /**
     * @param Context $context
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param StateInterface $state
     * @param ResultFactory $resultFactory
     * @param GenerateSharingUrl $generateSharingUrl
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $state,
        ResultFactory $resultFactory,
        GenerateSharingUrl $generateSharingUrl,
        MessageManagerInterface $messageManager
    ) {
        parent::__construct($context);
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $state;
        $this->resultFactory = $resultFactory;
        $this->generateSharingUrl = $generateSharingUrl;
        $this->messageManager = $messageManager;
    }

    /**
     * @return Redirect
     */
    public function execute() : Redirect
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $this->sendEmail();
        $resultRedirect->setRefererUrl();
        $this->messageManager->addSuccessMessage(
            "An Email has been sent to: " .
            $this->_request->getParam('receiver_name') . " (" . $this->_request->getParam('receiver_email') . ")"
        );
        return $resultRedirect;
    }

    /**
     * @return void
     */
    public function sendEmail() : void
    {
        $templateId = 'share_cart_items';
        $fromEmail = $this->_request->getParam('sender_email');
        $fromName = $this->_request->getParam('sender_name');
        $toEmail = $this->_request->getParam('receiver_email');
        $toName = $this->_request->getParam('receiver_name');
        $replyTo = $this->_request->getParam('sender_email');
        $message = $this->_request->getParam('message');
        $emailSubject = $this->scopeConfig->getValue('developerhub/share_cart/email_subject');
        $followUpText = $this->scopeConfig->getValue('developerhub/share_cart/email_followup_message');
        $shareCartUrl = '' . $this->generateSharingUrl->getEmailUrl() . '';
        try {
            // template variables pass here
            $templateVars = [
                'intro_text' => 'Hello ' . $toName,
                'from_info_text' => $fromName . ' (' . $fromEmail . ') has shared his shopping basket from ' . $this->storeManager->getStore()->getBaseUrl() . ' with you.',
                'message_text' => $message,
                'email_subject' => $emailSubject,
                'add_product_text' => $followUpText,
                'add_product_link' => $shareCartUrl
            ];

            $storeId = $this->storeManager->getStore()->getId();

            $from = ['email' => $fromEmail, 'name' => $fromName];
            $this->inlineTranslation->suspend();
            $templateOptions = [
                'area' => Area::AREA_FRONTEND,
                'store' => $storeId
            ];

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setReplyTo($replyTo)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFromByScope($from, $storeId)
                ->addTo($toEmail, $toName)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (Exception $e) {
            $this->_logger->info($e->getMessage());
        }
    }
}
