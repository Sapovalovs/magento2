<?php

namespace Dev\ProductComments\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\Store;
use Exception;

class EmailObserver implements ObserverInterface
{

    protected $transportBuilder;

    public function __construct(TransportBuilder $transportBuilder)
    {
        $this->transportBuilder = $transportBuilder;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $email = $observer->getData('sentEmail');
        $comment = $observer->getData('sentComment');

        $sender = [
            'name' => 'Magento admin',
            'email' => 'admin@magento.com',
        ];

        $templateParams = ['comment' => $comment];

        $transport = $this->transportBuilder
            ->setTemplateIdentifier('commentvisibility_email_template')
            ->setTemplateOptions(['area' => 'frontend', 'store' => Store::DEFAULT_STORE_ID])
            ->addTo($email)
            ->setTemplateVars($templateParams)
            ->setFrom($sender)
            ->getTransport();
        try {
            $transport->sendMessage();
        } catch (Exception $e) {
        }
        return $this;
    }
}