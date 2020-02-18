<?php

namespace Dev\ProductComments\Block\Widget;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Directory\Model\Currency;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use \Magento\Catalog\Helper\Image;

class Comments extends Template implements BlockInterface
{

    protected $_template = 'widget/Comments.phtml';
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;
    /**
     * @var SearchCriteriaBuilder
     */
    private $criteriaBuilder;

    /**
     * Posts constructor.
     *
     * @param Template\Context $context
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $criteriaBuilder
     */

    private $imageHelper;
    /**
     * @var Currency
     */
    private $currency;

    /**
     * Posts constructor.
     *
     * @param Template\Context           $context
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder      $criteriaBuilder
     * @param Image                      $imageHelper
     * @param Currency                   $currency
     * @param array                      $data
     */
    public function __construct(
        Template\Context $context,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        Image $imageHelper,
        Currency $currency,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->productRepository = $productRepository;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->imageHelper = $imageHelper;
        $this->currency = $currency;
    }

    public function getProductCollection($maxProducts)
    {
        $criteria = $this->criteriaBuilder
            ->addFilter('product_comments', 'yes')
            ->create()
            ->setPageSize($maxProducts);

        return $this->productRepository
            ->getList($criteria)
            ->getItems();
    }

    public function getItemImage($product)
    {
        $imageUrl = $this
            ->imageHelper
            ->init($product, 'product_base_image')
            ->getUrl();
        return $imageUrl;
    }

    public function getCurrencySymbol()
    {
        return $this->currency->getCurrencySymbol();
    }
}
