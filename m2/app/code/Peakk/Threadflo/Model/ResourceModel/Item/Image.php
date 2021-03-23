<?php

namespace Peakk\Threadflo\Model\ResourceModel\Item;

class Image extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * ResourceModel constructor.
     */
    protected function _construct()
    {
        $this->_init('threadflo_item_image', 'entity_id');
    }

    /**
     * Return ID column name.
     * 
     * @return string
     */
    public function getIdFieldName()
    {
        return 'entity_id';
    }

}