<?php

namespace Dev\ProductComments\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Catalog\Model\Product;

class ProductName extends Column
{
    protected $product;

    /**
     * ProductName constructor.
     *
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Product            $product
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Product $product,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->product = $product;
    }

    /**
     * @param $productId
     * @return string
     */
    public function getProductName($productId)
    {
        return $this->product->load($productId)->getName();
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);
        if (isset($dataSource['data']['items'])
        ) {
            foreach ($dataSource['data']['items'] as &$item
            ) {
                $productId = $item['product_id'];
                $item['product_id'] = $this->getProductName($productId);
            }
        }
        return $dataSource;
    }
}