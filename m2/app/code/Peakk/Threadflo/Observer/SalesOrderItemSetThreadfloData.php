<?php

namespace Peakk\Threadflo\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderItemSetThreadfloData implements ObserverInterface
{

    /**
     * @var \Magento\Quote\Model\Quote\ItemFactory
     */
    protected $_quoteItemFactory;

    /**
     * SalesOrderItemSetThreadfloData constructor.
     * 
     * @param \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory
     */
    public function __construct(
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory
    ) {
        $this->_quoteItemFactory = $quoteItemFactory;
    }
    
    /**
     * Set Threadflo ID and SKU on order item.
     * 
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        foreach ($order->getItems() as $orderItem) {
            $quoteItem = $this->_quoteItemFactory->create()->load($orderItem->getQuoteItemId());

            $orderItem->setThreadfloItemId($quoteItem->getThreadfloItemId());
            $orderItem->setThreadfloItemSku($quoteItem->getThreadfloItemSku());
            
            $orderItem->save();
        }
    }

}