<?php

class Peakk_Threadflo_Model_Resource_Item_Image_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    /**
     * Resource model constructor.
     */
    public function _construct()
    {
        $this->_init('threadflo/item_image');
    }

}