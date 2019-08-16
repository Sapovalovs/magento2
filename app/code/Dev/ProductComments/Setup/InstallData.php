<?php

namespace Dev\ProductComments\Setup;

use Dev\ProductComments\Model\Attribute\Source\ProductComments;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * Eav setup factory
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Init
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(\Magento\Eav\Setup\EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'product_comments',
            [
                'group' => 'General',
                'type' => 'int',
                'label' => 'Allow Comments',
                'input' => 'select',
                'source' => ProductComments::class,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'required' => true,
                'visible_on_front' => false,
                'is_filterable' => true

            ]
        );
    }
}