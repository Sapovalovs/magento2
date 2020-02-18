<?php

namespace Dev\ProductComments\Block;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Dev\ProductComments\Model\ResourceModel\Comment\CollectionFactory;
use Magento\Catalog\Api\ProductRepositoryInterface;

class View extends Template
{
    /**
     * @var Registry
     */
    private $registry;
    protected $commentFactory;
    private $productRepository;

    /**
     * View constructor.
     * @param Template\Context  $context
     * @param Registry          $registry
     * @param CollectionFactory $commentFactory
     * @param array             $data
     */
    public function __construct(
        Template\Context $context,
        Registry $registry,
        CollectionFactory $commentFactory,
        ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->commentFactory = $commentFactory;
        $this->productRepository = $productRepository;
    }
    public function getCurrentProduct()
    {
        return $this->registry->registry('current_product');
    }

    public function getCommentCollection($productId)
    {
        $comment = $this->commentFactory->create();
        $collection = $comment
            ->addFieldToFilter('product_id', $productId)
            ->addFieldToFilter('status', 'approved')
            ->getItems();
        return $collection;
    }

    public function getProductName($productId)
    {
        return $this->productRepository->getById($productId)->getName();
    }
}

