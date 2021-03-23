<?php

namespace Peakk\Threadflo\Model\ResourceModel\Item;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * ResourceModel constructor.
     */
    protected function _construct()
    {
        $this->_init('Peakk\Threadflo\Model\Item', 'Peakk\Threadflo\Model\ResourceModel\Item');
    }

}