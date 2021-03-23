<?php

namespace Peakk\Threadflo\Observer;

use Magento\Framework\Event\ObserverInterface;

class SalesOrderSendThreadfloData implements ObserverInterface
{

    /**
     * @var \Peakk\Threadflo\Model\Api
     */
    protected $_api;

    /**
     * @var \Peakk\Threadflo\Helper\Data
     */
    protected $_helper;

    /**
     * SalesOrderSendThreadfloData constructor.
     * 
     * @param \Peakk\Threadflo\Model\Api $api
     * @param \Peakk\Threadflo\Helper\Data $helper
     */
    public function __construct(
        \Peakk\Threadflo\Model\Api $api,
        \Peakk\Threadflo\Helper\Data $helper
    ) {
        $this->_api = $api;
        $this->_helper = $helper;
    }
    
    /**
     * Send order to Threadflo.
     * 
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->_helper->logTransaction('source: shell:sendOrderData:start');

        $order = $observer->getEvent()->getOrder();

        $this->_api->order($order);

        $this->_helper->logTransaction('source: shell:sendOrderData:end');
    }

}