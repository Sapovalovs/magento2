<?php

namespace Dev\ProductComments\Ui\Component\Listing\Column;

use Magento\Framework\Option\ArrayInterface;
use Dev\ProductComments\Block\Widget\Comments as ProductCollection;

class ProductList implements ArrayInterface
{
    protected $options;
    protected $productCollection;
    public function __construct(ProductCollection $productCollection)
    {
        $this->productCollection = $productCollection;
    }
    public function toOptionArray()
    {
        $result = [];
        $products = $this->productCollection->getProductCollection(10);
        foreach ($products as $product)
        {
            $result[] = [
                'value' => $product->getId(),
                'label' => $product->getName()];
        }
        return $result;
    }
}
