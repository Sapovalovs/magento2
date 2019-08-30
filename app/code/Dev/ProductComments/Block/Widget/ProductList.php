<?php

namespace Dev\ProductComments\Block\Widget;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\Widget\Html\Pager;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Catalog\Pricing\Price\FinalPrice;
use Magento\CatalogWidget\Model\Rule;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Pricing\Render;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Rule\Model\Condition\Sql\Builder;
use Magento\Widget\Block\BlockInterface;
use Magento\Widget\Helper\Conditions;

/**
 * Catalog Product List widget block
 * Class ProductList
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ProductList extends AbstractProduct implements BlockInterface, IdentityInterface
{
    /**
     * Default value for products per page
     */
    const DEFAULT_PRODUCTS_PER_PAGE = 4;
    /**
     * Default value whether show pager or not
     */
    const DEFAULT_SHOW_PAGER = false;
    /**
     * Instance of pager block
     *
     * @var Pager
     */
    protected $pager;
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;
    /**
     * Catalog product visibility
     *
     * @var Visibility
     */
    protected $catalogProductVisibility;
    /**
     * Product collection factory
     *
     * @var CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var Builder
     */
    protected $sqlBuilder;
    /**
     * @var Rule
     */
    protected $rule;
    /**
     * @var Conditions
     */
    protected $conditionsHelper;

    /**
     * @param Context $context
     * @param CollectionFactory $productCollectionFactory
     * @param Visibility $catalogProductVisibility
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param Builder $sqlBuilder
     * @param Rule $rule
     * @param Conditions $conditionsHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        Builder $sqlBuilder,
        Rule $rule,
        Conditions $conditionsHelper,
        array $data = []
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->httpContext = $httpContext;
        $this->sqlBuilder = $sqlBuilder;
        $this->rule = $rule;
        $this->conditionsHelper = $conditionsHelper;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getProductPriceHtml(
        Product $product,
        $priceType = null,
        $renderZone = Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;
        /** @var Render $priceRender */
        $priceRender = $this->getLayout()->getBlock('product.price.render.default');
        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }

    /**
     * Render pagination HTML
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPagerHtml()
    {
        if ($this->showPager() && $this->getProductCollection()->getSize() > $this->getProductsPerPage()) {
            if (!$this->pager) {
                $this->pager = $this->getLayout()->createBlock(
                    Pager::class,
                    'widget.product.comments.pager'
                );
                $this->pager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    ->setPageVarName($this->getData('page_var_name'))
                    ->setLimit($this->getProductsPerPage())
                    ->setTotalLimit($this->getProductsCount())
                    ->setCollection($this->getProductCollection());
            }
            if ($this->pager instanceof AbstractBlock) {
                return $this->pager->toHtml();
            }
        }
        return '';
    }

    /**
     * Return identifiers for produced content
     *
     * @return array
     */
    public function getIdentities()
    {
        $identities = [];
        if ($this->getProductCollection()) {
            foreach ($this->getProductCollection() as $product) {
                if ($product instanceof IdentityInterface) {
                    $identities = array_merge($identities, $product->getIdentities());
                }
            }
        }
        return $identities ?: [Product::CACHE_TAG];
    }

    /**
     * Get value of widgets' title parameter
     *
     * @return mixed|string
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $this->setProductCollection($this->createCollection());
        return parent::_beforeToHtml();
    }

    /**
     * Prepare and return product collection
     *
     * @return Collection
     */
    public function createCollection()
    {
        /** @var $collection Collection */
        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());
        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->setPageSize($this->getPageSize())
            ->addAttributeToFilter('product_comments', 1)
            ->setCurPage($this->getRequest()->getParam($this->getData('page_var_name'), 1));

        /**
         * Prevent retrieval of duplicate records. This may occur when multiselect product attribute matches
         * several allowed values from condition simultaneously
         */
        $collection->distinct(true);
        return $collection;
    }

    /**
     * Retrieve how many products should be displayed on page
     *
     * @return int
     */
    protected function getPageSize()
    {
        return $this->showPager() ? $this->getProductsPerPage() : $this->getProductsCount();
    }

    /**
     * Return flag whether pager need to be shown or not
     *
     * @return bool
     */
    public function showPager()
    {
        if (!$this->hasData('show_pager')) {
            $this->setData('show_pager', self::DEFAULT_SHOW_PAGER);
        }
        return (bool)$this->getData('show_pager');
    }

    /**
     * Retrieve how many products should be displayed
     *
     * @return int
     */
    public function getProductsPerPage()
    {
        if (!$this->hasData('products_per_page')) {
            $this->setData('products_per_page', self::DEFAULT_PRODUCTS_PER_PAGE);
        }
        return $this->getData('products_per_page');
    }

    /**
     * Retrieve how many products should be displayed
     *
     * @return int
     */
    public function getProductsCount()
    {
        return $this->getData('products_count');
    }
}
