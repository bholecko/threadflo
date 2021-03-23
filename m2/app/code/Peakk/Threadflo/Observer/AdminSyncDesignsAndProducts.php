<?php

namespace Peakk\Threadflo\Observer;

class AdminSyncDesignsAndProducts
{

    /**
     * @var \Peakk\Threadflo\Model\Api\Products
     */
    protected $_api;

    /**
     * @var \Peakk\Threadflo\Model\Processor\Catalog
     */
    protected $_processor;

    /**
     * @var \Peakk\Threadflo\Helper\Data
     */
    protected $_helper;

    /**
     * AdminSyncDesignsAndProducts constructor.
     *
     * @param \Peakk\Threadflo\Model\Api\Products $api
     * @param \Peakk\Threadflo\Model\Processor\Catalog $processor
     * @param \Peakk\Threadflo\Helper\Data $helper
     */
    public function __construct(
        \Peakk\Threadflo\Model\Api\Products $api,
        \Peakk\Threadflo\Model\Processor\Catalog $processor,
        \Peakk\Threadflo\Helper\Data $helper
    ) {
        $this->_api = $api;
        $this->_processor = $processor;
        $this->_helper = $helper;
    }

    /**
     * Import Threadflo product items then update Magento catalog.
     */
    public function execute()
    {
        $this->_helper->logTransaction('source: observer:syncDesignsAndProducts:start');

        $this->_api->importItems();
        $this->_processor->createProducts();

        $this->_helper->logTransaction('source: observer:syncDesignsAndProducts:end');
    }

}