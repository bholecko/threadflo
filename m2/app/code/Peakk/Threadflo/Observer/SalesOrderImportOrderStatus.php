<?php

namespace Peakk\Threadflo\Observer;

class SalesOrderImportOrderStatus
{

    /**
     * @var \Peakk\Threadflo\Model\Api\Orders
     */
    protected $_api;

    /**
     * @var \Peakk\Threadflo\Helper\Data
     */
    protected $_helper;

    /**
     * SalesOrderImportOrderStatus constructor.
     * 
     * @param \Peakk\Threadflo\Model\Api\Orders $api
     * @param \Peakk\Threadflo\Helper\Data $helper
     */
    public function __construct(
        \Peakk\Threadflo\Model\Api\Orders $api,
        \Peakk\Threadflo\Helper\Data $helper
    ) {
        $this->_api = $api;
        $this->_helper = $helper;
    }
    
    /**
     * Import order status values for threadflo_order_status. 
     */
    public function execute()
    {
        $this->_helper->logTransaction('source: observer:importOrderStatus:start');

        $this->_api->importOrderStatus();

        $this->_helper->logTransaction('source: observer:importOrderStatus:end');
    }

}