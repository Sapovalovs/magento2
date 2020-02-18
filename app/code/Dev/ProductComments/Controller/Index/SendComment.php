<?php

namespace Dev\ProductComments\Controller\Index;

use Dev\ProductComments\Model\Comment;
use Dev\ProductComments\Model\ResourceModel\Comment as ResourceComment;
use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Zend_Validate;
use Zend_Validate_Exception;

class SendComment extends Action
{
    /**
     * @var Comment
     */
    private $commentModel;
    /**
     * @var ResourceComment
     */
    private $resourceModel;

    /**
     * SendComment constructor.
     * @param Context $context
     * @param Comment $commentModel
     * @param ResourceComment $resourceModel
     */
    public function __construct(
        Context $context,
        Comment $commentModel,
        ResourceComment $resourceModel
    ) {
        parent::__construct($context);
        $this->commentModel = $commentModel;
        $this->resourceModel = $resourceModel;
    }

    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $name = $this->getRequest()->getParam('name');
        $email = $this->getRequest()->getParam('email');
        $comment = $this->getRequest()->getParam('comment');
        $productId = $this->getRequest()->getParam('productId');

        try {
            if (!Zend_Validate::is($name, 'NotEmpty')) {
                $this->messageManager->addErrorMessage('Name can not be Empty');
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }

            if (!Zend_Validate::is($comment, 'NotEmpty')) {
                $this->messageManager->addErrorMessage('Comment can not be Empty');
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            } elseif (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->messageManager->addErrorMessage('Email address not valid');
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            } else {
                $this->commentModel
                    ->setData('name', $name)
                    ->setData('email', $email)
                    ->setData('comment', $comment)
                    ->setData('status', 'not approved')
                    ->setData('product_id', $productId);

                try {
                    $this->resourceModel->save($this->commentModel);
                } catch (Exception $e) {
                }
                $this->messageManager
                    ->addSuccessMessage(
                        'Comment request has been sent. Wait for admin approval'
                    );
                $resultRedirect->setUrl($this->_redirect->getRefererUrl());
                return $resultRedirect;
            }
        } catch (Zend_Validate_Exception $e) {
        }
        exit;
    }
}
