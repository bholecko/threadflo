<?php

namespace Peakk\Threadflo\Setup;

use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Model\Entity\AttributeFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var AttributeFactory
     */
    private $entityAttributeFactory;

    /**
     * InstallData constructor.
     * 
     * @param EavSetupFactory $eavSetupFactory
     * @param AttributeFactory $entityAttributeFactory
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        AttributeFactory $entityAttributeFactory
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->entityAttributeFactory = $entityAttributeFactory;
    }

    /**
     * Install Threadflo product attributes.
     * 
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'threadflo_item_id',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Threadflo Item ID',
                'group' => 'Design',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => false,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'threadflo_item_sku',
            [
                'type' => 'varchar',
                'backend' => '',
                'frontend' => '',
                'label' => 'Threadflo Item SKU',
                'group' => 'Design',
                'input' => 'text',
                'class' => '',
                'source' => '',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => false,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => true,
                'unique' => false
            ]
        );

        $shirtSizeSource = new \Peakk\Threadflo\Model\System\Config\Source\Shirt\Size;

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'threadflo_item_size',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Size',
                'group' => 'Design',
                'input' => 'select',
                'class' => '',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => false,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => '',
                'option' => ['values' => $shirtSizeSource->getOption()]
            ]
        );

        $shirtColorSource = new \Peakk\Threadflo\Model\System\Config\Source\Shirt\Color;

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'threadflo_item_color',
            [
                'type' => 'int',
                'backend' => '',
                'frontend' => '',
                'label' => 'Color',
                'group' => 'Design',
                'input' => 'select',
                'class' => '',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'visible' => false,
                'required' => false,
                'user_defined' => true,
                'default' => null,
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => true,
                'used_in_product_listing' => true,
                'unique' => false,
                'apply_to' => '',
                'option' => ['values' => $shirtColorSource->getOption()]
            ]
        );
    }

}