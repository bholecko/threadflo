<?php

$this->startSetup();

if (!$this->getConnection()->isTableExists($this->getTable('threadflo/item'))) {
    $table1 = $this->getConnection()
        ->newTable($this->getTable('threadflo/item'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'unsigned'  => true,
            'nullable'  => false,
            'primary'   => true,
            'auto_increment' => true
        ), 'Entity ID')
        ->addColumn('threadflo_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'unsigned'  => true,
            'nullable'  => false
        ), 'Threadflo Item ID')
        ->addColumn('item_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 80, array(
            'nullable'  => true
        ), 'Item Name')
        ->addColumn('category_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
            'nullable'  => true
        ), 'Category Name')
        ->addColumn('parent_sku', Varien_Db_Ddl_Table::TYPE_VARCHAR, 25, array(
            'nullable'  => true
        ), 'Parent SKU')
        ->addColumn('sku', Varien_Db_Ddl_Table::TYPE_VARCHAR, 25, array(
            'nullable'  => true
        ), 'SKU')
        ->addColumn('color_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(
            'nullable'  => true
        ), 'Color Name')
        ->addColumn('size_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 10, array(
            'nullable'  => true
        ), 'Size Name')
        ->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
            'default'   => '0.0000',
            'nullable'  => true
        ), 'Price')
        ->setComment('Threadflo Imported Items');

    $this->getConnection()->createTable($table1);
}

if (!$this->getConnection()->isTableExists($this->getTable('threadflo/item_image'))) {
    $table2 = $this->getConnection()
        ->newTable($this->getTable('threadflo/item_image'))
        ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'auto_increment' => true
        ), 'Entity ID')
        ->addColumn('threadflo_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
            'unsigned' => true,
            'nullable' => false
        ), 'Threadflo Item ID')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 25, array(
            'nullable' => true
        ), 'Name')
        ->addColumn('url', Varien_Db_Ddl_Table::TYPE_VARCHAR, 100, array(
            'nullable' => true
        ), 'URL')
        ->setComment('Threadflo Imported Item Images');

    $this->getConnection()->createTable($table2);
}

$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'threadflo_item_id', array(
    'type'          => 'int',
    'input'         => 'text',
    'group'         => 'Design',
    'label'         => 'Threadflo Item ID',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => true,
    'is_user_defined'  => true,
    'default'       => null,
    'visible_on_front' => false,
    'apply_to' => 'simple,configurable'
));

$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'threadflo_item_sku', array(
    'type'          => 'varchar',
    'input'         => 'text',
    'group'         => 'Design',
    'label'         => 'Threadflo Item SKU',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => true,
    'is_user_defined'  => true,
    'default'       => null,
    'visible_on_front' => false,
    'apply_to' => 'simple,configurable'
));

$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'threadflo_item_color', array(
    'type'          => 'int',
    'input'         => 'select',
    'source'        => 'eav/entity_attribute_source_table',
    'frontend'      => '',
    'group'         => 'Design',
    'label'         => 'Color',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => true,
    'is_user_defined'  => true,
    'used_in_product_listing' => true,
    'default'       => null,
    'visible_on_front' => false,
    'option'        => array (
        'values' => Mage::getModel('threadflo/system_config_source_shirt_color')->toAttributeArray()
    ),
    'apply_to' => 'simple',
    'is_configurable' => true
));

$this->addAttribute(Mage_Catalog_Model_Product::ENTITY, 'threadflo_item_size', array(
    'type'          => 'int',
    'input'         => 'select',
    'source'        => 'eav/entity_attribute_source_table',
    'frontend'      => '',
    'group'         => 'Design',
    'label'         => 'Size',
    'visible'       => true,
    'required'      => false,
    'user_defined'  => true,
    'is_user_defined'  => true,
    'used_in_product_listing' => true,
    'default'       => null,
    'visible_on_front' => false,
    'option'        => array (
        'values' => Mage::getModel('threadflo/system_config_source_shirt_size')->toAttributeArray()
    ),
    'apply_to' => 'simple',
    'is_configurable' => true
));

$newAttribute1 = $this->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'threadflo_item_id');
$newAttribute2 = $this->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'threadflo_item_sku');
$newAttribute4 = $this->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'threadflo_item_color');
$newAttribute3 = $this->getAttribute(Mage_Catalog_Model_Product::ENTITY, 'threadflo_item_size');

$attributeSets = Mage::getModel('eav/entity_attribute_set')->getCollection()
    ->addFieldToFilter('entity_type_id', Mage_Catalog_Model_Product::ENTITY)
    ->load();

foreach ($attributeSets as $attributeSet) {
    $attributeSetId = $attributeSet->getId();
    $attributeSetGroup = $this->getAttributeGroup(Mage_Catalog_Model_Product::ENTITY, $attributeSetId, 'Design');

    if ($attributeSetGroup) {
        $this->addAttributeToSet(Mage_Catalog_Model_Product::ENTITY, $attributeSetId, $attributeSetGroup['attribute_group_id'], $newAttribute1['attribute_id']);
        $this->addAttributeToSet(Mage_Catalog_Model_Product::ENTITY, $attributeSetId, $attributeSetGroup['attribute_group_id'], $newAttribute2['attribute_id']);
        $this->addAttributeToSet(Mage_Catalog_Model_Product::ENTITY, $attributeSetId, $attributeSetGroup['attribute_group_id'], $newAttribute3['attribute_id']);
        $this->addAttributeToSet(Mage_Catalog_Model_Product::ENTITY, $attributeSetId, $attributeSetGroup['attribute_group_id'], $newAttribute4['attribute_id']);
    }
}

$this->getConnection()->addColumn($this->getTable('sales/quote_item'), 'threadflo_item_id', 'int(10) UNSIGNED');
$this->getConnection()->addColumn($this->getTable('sales/quote_item'), 'threadflo_item_sku', 'varchar(25) NULL');
$this->getConnection()->addColumn($this->getTable('sales/order_item'), 'threadflo_item_id', 'int(10) UNSIGNED');
$this->getConnection()->addColumn($this->getTable('sales/order_item'), 'threadflo_item_sku', 'varchar(25) NULL');
$this->getConnection()->addColumn($this->getTable('sales/order'), 'threadflo_order_id', 'int(10) UNSIGNED');
$this->getConnection()->addColumn($this->getTable('sales/order'), 'threadflo_order_status', 'varchar(10) NULL');

$this->endSetup();
