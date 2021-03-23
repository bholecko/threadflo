<?php

class Peakk_Threadflo_Model_Resource_Item_Image extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Resource model constructor.
     */
    protected function _construct()
    {
        $this->_init('threadflo/item_image', 'entity_id');
    }

}