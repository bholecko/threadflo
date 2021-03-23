<?php

namespace Peakk\Threadflo\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Platform ID.
     */
    const PLATFORM_ID = 'Magento2';

    /**
     * XML paths.
     */
    const API_ENABLED_XML_PATH = 'threadflo_general/account/api_enabled';
    const API_KEY_XML_PATH = 'threadflo_general/account/api_key';

    /**
     * @var \Peakk\Threadflo\Model\Item\ImageFactory
     */
    protected $_itemImageFactory;

    /**
     * @var \Peakk\Threadflo\Logger\Debug
     */
    protected $_debugLogger;

    /**
     * @var \Peakk\Threadflo\Logger\Error
     */
    protected $_errorLogger;

    /**
     * @var \Peakk\Threadflo\Logger\Transaction
     */
    protected $_transactionLogger;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_productFactory;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Peakk\Threadflo\Model\Item\ImageFactory $itemImageFactory
     * @param \Peakk\Threadflo\Logger\Debug $debugLogger
     * @param \Peakk\Threadflo\Logger\Error $errorLogger
     * @param \Peakk\Threadflo\Logger\Transaction $transactionLogger
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Peakk\Threadflo\Model\Item\ImageFactory $itemImageFactory,
        \Peakk\Threadflo\Logger\Debug $debugLogger,
        \Peakk\Threadflo\Logger\Error $errorLogger,
        \Peakk\Threadflo\Logger\Transaction $transactionLogger,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->_itemImageFactory = $itemImageFactory;
        $this->_debugLogger = $debugLogger;
        $this->_errorLogger = $errorLogger;
        $this->_transactionLogger = $transactionLogger;
        $this->_productFactory = $productFactory;
        parent::__construct($context);
    }

    /**
     * Return color value given a color label.
     *
     * @param $colorName
     * @return mixed
     */
    public function getColorCode($colorName)
    {
        $threadfloItemColorAttr = $this->_productFactory->create()->getResource()->getAttribute('threadflo_item_color');

        foreach ($threadfloItemColorAttr->getSource()->getAllOptions() as $option) {
            if ($option['label'] == $colorName) {
                return $option['value'];
            }
        }
    }

    /**
     * Return size value given a size label.
     *
     * @param $sizeName
     * @return mixed
     */
    public function getSizeCode($sizeName)
    {
        $threadfloItemSizeAttr = $this->_productFactory->create()->getResource()->getAttribute('threadflo_item_size');

        foreach ($threadfloItemSizeAttr->getSource()->getAllOptions() as $option) {
            if ($option['label'] == $sizeName) {
                return $option['value'];
            }
        }
    }

    /**
     * Return all images for a Threadflo item.
     *
     * @param $threadfloItem
     * @return $this
     */
    public function getImages($threadfloItem)
    {
        return $this->_itemImageFactory->create()->getCollection()
                ->addFieldToSelect('*')
                ->addFieldToFilter('threadflo_item_id', $threadfloItem->getId())
                ->load();
    }

    /**
     * Return the website domain.
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->scopeConfig->getValue(\Magento\Config\Model\Config\Backend\Admin\Custom::XML_PATH_UNSECURE_BASE_URL);
    }

    /**
     * Return true if the API is enabled via system config.
     *
     * @return boolean
     */
    public function isApiEnabled()
    {
        return $this->scopeConfig->getValue(self::API_ENABLED_XML_PATH);
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

    // Return API key
    public function getApiKey()
    {
        return trim($this->scopeConfig->getValue(self::API_KEY_XML_PATH));
    }

    /**
     * Log regular message
     *
     * @param string $message
     */
    public function log($message)
    {
        $this->_debugLogger->addDebug($message);
    }

    /**
     * Log error message.
     *
     * @param string $message
     */
    public function logError($message)
    {
        $this->_errorLogger->addError($message);
    }

    /**
     * Log transaction message.
     *
     * @param string $message
     */
    public function logTransaction($message)
    {
        $this->transactionLogger->addDebug($message.' '.$this->getTimestamp());
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

}