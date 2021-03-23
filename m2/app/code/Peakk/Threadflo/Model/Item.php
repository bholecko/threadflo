<?php

namespace Peakk\Threadflo\Model;

class Item extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var \Peakk\Threadflo\Model\ResourceModel\Item\Image\Collection
     */
    protected $_itemImages;

    /**
     * @var \Peakk\Threadflo\Model\Item\ImageFactory
     */
    protected $_itemImageFactory;

    /**
     * Item constructor.
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\Item $resource
     * @param ResourceModel\Item\Collection $resourceCollection
     * @param Item\ImageFactory $itemImageFactory
     * @param array $data
     */
    public function __contruct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Peakk\Threadflo\Model\ResourceModel\Item $resource,
        \Peakk\Threadflo\Model\ResourceModel\Item\Collection $resourceCollection,
        \Peakk\Threadflo\Model\Item\ImageFactory $itemImageFactory,
        array $data = []
    ) {
        $this->_itemImageFactory = $itemImageFactory;

        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * ResourceModel constructor.
     */
    protected function _construct()
    {
        $this->_init('Peakk\Threadflo\Model\ResourceModel\Item');
    }

    /**
     * Return Threadflo image data.
     *
     * @return $this|ResourceModel\Item\Image\Collection
     */
    public function getImages()
    {
        if (!$this->_itemImages) {
            $this->_itemImages = $this->_itemImageFactory->create()->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('threadflo_item_id', $this->getId())
                ->load();
        }

        return $this->_itemImages;
    }

    /**
     * Delete all images for this item.
     */
    public function deleteImages()
    {
        if ($this->_itemImages) {
            foreach ($this->_itemImages as $image) {
                $image->delete();
            }
        }

        $this->_itemImages = null;
    }

}