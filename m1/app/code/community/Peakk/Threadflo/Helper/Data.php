<?php

class Peakk_Threadflo_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Platform ID.
     */
    const PLATFORM_ID = 'Magento1';

    /**
     * Custom log file names.
     */
    const LOG_FILE = 'threadflo_system.log';
    const LOG_ERROR_FILE = 'threadflo_error.log';
    const LOG_TRANSACTIONS_FILE = 'threadflo_transactions.log';

    /**
     * System config XML paths.
     */
    const API_ENABLED_XML_PATH = 'threadflo_general/account/api_enabled';
    const API_KEY_XML_PATH = 'threadflo_general/account/api_key';
    const SHIPPING_TRACKING_ENABLED_XML_PATH = 'threadflo_general/services/shipping_tracking_enabled';
    const SYNC_DESIGNS_ENABLED_XML_PATH = 'threadflo_general/services/sync_designs_and_products_enabled';
    
    /**
     * Return color value given a color label
     * 
     * @param string $colorName
     * @return string
     */
    public function getColorCode($colorName)
    {
        $threadfloItemColorAttribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'threadflo_item_color');
        $threadfloItemColorAttributeOptions = Mage::getModel('eav/entity_attribute_option')->getCollection()
            ->setStoreFilter()
            ->setAttributeFilter($threadfloItemColorAttribute->getAttributeId())
            ->load()
            ->toOptionArray();

        foreach ($threadfloItemColorAttributeOptions as $threadfloItemColorAttributeOption) {
            if ($threadfloItemColorAttributeOption['label'] == $colorName) {
                return $threadfloItemColorAttributeOption['value'];
            }
        }

        return '';
    }

    /**
     * Return size value given a size label.
     * 
     * @param string $sizeName
     * @return string
     */
    public function getSizeCode($sizeName)
    {
        $threadfloItemSizeAttribute = Mage::getModel('eav/entity_attribute')->loadByCode(Mage_Catalog_Model_Product::ENTITY, 'threadflo_item_size');
        $threadfloItemSizeAttributeOptions = Mage::getModel('eav/entity_attribute_option')->getCollection()
            ->setStoreFilter()
            ->setAttributeFilter($threadfloItemSizeAttribute->getAttributeId())
            ->load()
            ->toOptionArray();

        foreach ($threadfloItemSizeAttributeOptions as $threadfloItemSizeAttributeOption) {
            if ($threadfloItemSizeAttributeOption['label'] == $sizeName) {
                return $threadfloItemSizeAttributeOption['value'];
            }
        }

        return '';
    }

    /**
     * Return all images for a Threadflo item.
     * 
     * @param Peakk_Threadflo_Model_Item $threadfloItem
     * @return Peakk_Threadflo_Model_Resource_Item_Collection
     */
    public function getImages($threadfloItem)
    {
        return Mage::getModel('threadflo/item_image')->getCollection()
            ->addFieldToSelect('*')
            ->addFieldToFilter('threadflo_item_id', $threadfloItem->getId())
            ->load();
    }

    /**
     * Return true if the API is enabled via system config.
     * 
     * @return bool
     */
    public function isApiEnabled()
    {
        return Mage::getStoreConfigFlag(self::API_ENABLED_XML_PATH);
    }

    /**
     * Return a unique platform ID.
     * 
     * @return string
     */
    public function getPlatformId()
    {
        return self::PLATFORM_ID;
    }

    /**
     * Return true if the API key is set.
     * 
     * @return bool
     */
    public function isApiConfigured()
    {
        $apiKey = $this->getApiKey();

        return isset($apiKey);
    }

    /**
     * Return API key.
     * 
     * @return string
     */
    public function getApiKey()
    {
        return trim(Mage::getStoreConfig(self::API_KEY_XML_PATH));
    }

    /**
     * Return true if automatic shipping tracking imports are enabled.
     *
     * @return bool
     */
    public function isAutoShippingTrackingEnabled()
    {
        return Mage::getStoreConfigFlag(self::SHIPPING_TRACKING_ENABLED_XML_PATH);
    }

    /**
     * Return true if automatic Threadflo design syncs are enabled.
     *
     * @return bool
     */
    public function isAutoSyncDesignsAndProductsEnabled()
    {
        return Mage::getStoreConfigFlag(self::SYNC_DESIGNS_AND_PRODUCTS_ENABLED_XML_PATH);
    }

    /**
     * Log regular message.
     * 
     * @param string $message
     */
    public function log($message)
    {
        Mage::log($message, null, self::LOG_FILE);
    }

    /**
     * Log error message.
     * 
     * @param string $message
     */
    public function logError($message)
    {
        Mage::log($message, null, self::LOG_ERROR_FILE);
    }

    /**
     * Log transaction message.
     * 
     * @param string $message
     */
    public function logTransaction($message)
    {
        Mage::log($message.' '.$this->getTimestamp(), null, self::LOG_TRANSACTIONS_FILE);
    }

    /**
     * Return current timestamp.
     * 
     * @return string
     */
    private function getTimestamp()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * Return true if Threadflo items exist in the quote.
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return bool
     */
    public function isThreadfloQuote($quote)
    {
        $quoteItems = $quote->getItemsCollection();

        if ($quoteItems) {
            foreach ($quoteItems as $quoteItem) {
                if ($quoteItem->getThreadfloItemId()) {
                    return true;
                }
            }
        }

        return false;
    }

}