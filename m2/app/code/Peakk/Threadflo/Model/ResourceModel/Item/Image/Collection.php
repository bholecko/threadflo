<?php

namespace Peakk\Threadflo\Model\ResourceModel\Item\Image;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * ResourceModel constructor.
     */
    protected function _construct()
    {
        $this->_init('Peakk\Threadflo\Model\Item\Image', 'Peakk\Threadflo\Model\ResourceModel\Item\Image');
    }

}