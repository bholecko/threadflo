<?php

namespace Peakk\Threadflo\Model\Item;

class Image extends \Magento\Framework\Model\AbstractModel
{

    /**
     * Image constructor.
     * 
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Peakk\Threadflo\Model\ResourceModel\Item\Image $resource
     * @param \Peakk\Threadflo\Model\ResourceModel\Item\Image\Collection $resourceCollection
     */
    public function __contruct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Peakk\Threadflo\Model\ResourceModel\Item\Image $resource,
        \Peakk\Threadflo\Model\ResourceModel\Item\Image\Collection $resourceCollection
    ) {
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection
        );
    }

    /**
     * ResourceModel constructor.
     */
    protected function _construct()
    {
        $this->_init('Peakk\Threadflo\Model\ResourceModel\Item\Image');
    }

}