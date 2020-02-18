<?php

namespace Dev\ProductComments\Controller\Adminhtml;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Registry;

abstract class Comment extends Action
{

    const ADMIN_RESOURCE = 'Dev_ProductComments::top_level';

    protected $coreRegistry;

    public function __construct(
        Context $context,
        Registry $_coreRegistry
    ) {
        $this->coreRegistry = $_coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param  Page $resultPage
     * @return Page
     */
    public function initPage($resultPage)
    {
        $resultPage->setActiveMenu(self::ADMIN_RESOURCE)
            ->addBreadcrumb(__('Dev'), __('Dev'))
            ->addBreadcrumb(__('Product Comment'), __('Product Comment'));
        return $resultPage;
    }
}
