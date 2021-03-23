<?php

class Peakk_Threadflo_Model_Item extends Mage_Core_Model_Abstract
{

    /**
     * @var string
     */
    protected $_eventPrefix = 'threadflo_item';

    /**
     * @var string
     */
    protected $_cacheTag = 'threadflo_item';

    /**
     * Resource model constructor.
     */
    protected function _construct()
    {
        $this->_init('threadflo/item');
    }

    /**
     * Return Threadflo image data.
     * 
     * @return Peakk_Threadflo_Model_Resource_Item_Collection
     */
    public function getImages()
    {
        return Mage::getModel('threadflo/item_image')->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('threadflo_item_id', $this->getId())
                ->load();
    }

    /**
     * Delete all images for this item.
     */
    public function deleteImages()
    {
        $images = $this->getImages();

        if ($images) {
            foreach ($images as $image) {
                $image->delete();
            }
        }
    }

}