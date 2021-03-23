<?php

namespace Peakk\Threadflo\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesQuoteItemSetThreadfloData implements ObserverInterface
{
    
    /**
     * Set Threadflo ID and SKU on quote item.
     * 
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $quoteItem = $observer->getEvent()->getQuoteItem();
        $product = $observer->getEvent()->getProduct();

        $quoteItem->setThreadfloItemId((int)$product->getThreadfloItemId());
        $quoteItem->setThreadfloItemSku((string)$product->getThreadfloItemSku());
    }

}