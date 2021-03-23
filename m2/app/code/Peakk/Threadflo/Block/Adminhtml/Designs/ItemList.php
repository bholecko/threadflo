<?php

namespace Peakk\Threadflo\Block\Adminhtml\Designs;

class ItemList extends \Magento\Backend\Block\Template
{

    /**
     * Collection of all Threadflo items.
     *
     * @var \Peakk\Threadflo\Model\ResourceModel\Item\Collection
     */
    protected $_items;

    /**
     * ItemList constructor.
     * 
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Peakk\Threadflo\Model\ItemFactory $itemFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Peakk\Threadflo\Model\ItemFactory $itemFactory,
        array $data = []
    ) {
        $this->setTemplate('Peakk_Threadflo::designs/list.phtml');
        $this->_items = $itemFactory->create()->getCollection()->load();
        parent::__construct($context, $data);
    }

    /**
     * Return all Threadflo items.
     *
     * @return \Peakk\Threadflo\Model\ResourceModel\Item\Collection
     */
    public function getItems()
    {
        return $this->_items;
    }

}