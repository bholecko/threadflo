<?php

class Peakk_Threadflo_Model_Resource_Item extends Mage_Core_Model_Resource_Db_Abstract
{

    /**
     * Resource model constructor.
     */
    protected function _construct()
    {
        $this->_init('threadflo/item', 'entity_id');
    }

}