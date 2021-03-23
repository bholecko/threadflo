<?php

class Peakk_Threadflo_Model_Observer
{

    /**
     * Set Threadflo ID and SKU on quote item.
     *
     * @param $observer
     */
    public function salesQuoteItemSetThreadfloItemId($observer)
    {
        $quoteItem = $observer->getEvent()->getQuoteItem();
        $product = $observer->getEvent()->getProduct();

        $quoteItem->setThreadfloItemId((int)$product->getThreadfloItemId());
        $quoteItem->setThreadfloItemSku((string)$product->getThreadfloItemSku());
    }

    /**
     * Send order to Threadflo.
     * 
     * @param $observer
     */
    public function sendThreadfloOrder($observer)
    {
        $helper = Mage::helper('threadflo');
        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        
        if ($helper->isThreadfloQuote($quote)) {
            $helper->logTransaction('source: observer:sendThreadfloOrder:start');

            $apiProducts = Mage::getModel('threadflo/api_products');
            $apiOrders = Mage::getModel('threadflo/api_orders');

            $itemsExistInThreadfloMapping = $apiProducts->itemsExist($quote->getItemsCollection());

            if ($itemsExistInThreadfloMapping) {
                $itemsMissingInThreadflo = array();

                foreach ($itemsExistInThreadfloMapping as $itemExistsId => $itemExistsBoolean) {
                    if (!$itemExistsBoolean) {
                        $itemsMissingInThreadflo[] = $itemExistsId;
                    }
                }

                if ($itemsMissingInThreadflo) {
                    $errorMsg = 'Items currently out of stock: ';

                    foreach ($itemsMissingInThreadflo as $ctr => $itemMissingId) {
                        $quoteItem = Mage::getModel('sales/quote_item')->load($itemMissingId);

                        $errorMsg .= ($ctr >= 1 ? ', ' : '') . $quoteItem->getName();
                    }

                    $errorMsg .= '. Please remove these items from cart to place order.';

                    throw new Exception($errorMsg);
                }
            }

            $apiOrders->order($order);

            $helper->logTransaction('source: observer:sendThreadfloOrder:end');
        }
    }

    /**
     * Import shipping tracking number(s) and create shipment for pending Threadflo orders.
     */
    public function importShippingTracking()
    {
        $helper = Mage::helper('threadflo');

        if ($helper->isAutoShippingTrackingEnabled()) {
            $helper->logTransaction('source: observer:importShippingTracking:start');

            Mage::getModel('threadflo/api_orders')->importShippingTracking();

            $helper->logTransaction('source: observer:importShippingTracking:end');
        }
    }

}