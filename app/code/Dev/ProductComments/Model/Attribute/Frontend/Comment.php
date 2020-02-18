<?php
namespace Dev\ProductComments\Model\Attribute\Frontend;

use Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend;
use Magento\Framework\DataObject;

class Comment extends AbstractFrontend
{
    /**
     * @param DataObject $object
     * @return mixed|string
     */
    public function getValue(DataObject $object)
    {
        $value = $object->getData($this->getAttribute()->getAttributeCode());
        return "<b>$value</b>";
    }
}
