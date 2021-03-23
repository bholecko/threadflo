<?php

class Peakk_Threadflo_Model_Item_Image extends Mage_Core_Model_Abstract
{

    /**
     * @var string
     */
    protected $_eventPrefix = 'threadflo_item_image';

    /**
     * @var string
     */
    protected $_cacheTag = 'threadflo_item_image';

    /**
     * Resource model constructor.
     */
    protected function _construct()
    {
        $this->_init('threadflo/item_image');
    }

}