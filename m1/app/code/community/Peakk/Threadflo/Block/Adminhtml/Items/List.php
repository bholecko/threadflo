<?php

class Peakk_Threadflo_Block_Adminhtml_Items_List extends Mage_Adminhtml_Block_Template
{

    /**
     * Return all Threadflo design items.
     *
     * @return Peakk_Threadflo_Model_Resource_Item_Collection
     */
    public function getDesigns()
    {
        return Mage::getModel('threadflo/item')->getCollection()
            ->addFieldToFilter('threadflo_item_id', array('neq' => null))
            ->addFieldToFilter('threadflo_item_id', array('neq' => ''))
            ->load();
    }

}