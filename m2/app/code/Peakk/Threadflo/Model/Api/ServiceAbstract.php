<?php

namespace Peakk\Threadflo\Model\Api;

class ServiceAbstract
{

    /**
     * API URL constants.
     */
    const API_CONNECTOR_URL = 'https://login.threadflo.com/api/connector/';

    /**
     * @var \Peakk\Threadflo\Helper\Data
     */
    protected $_helper;

    /**
     * @var ItemFactory
     */
    protected $_itemFactory;

    /**
     * @var \Peakk\Threadflo\Model\Item\ImageFactory
     */
    protected $_itemImageFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $_regionFactory;

    /**
     * ServiceAbstract constructor.
     * 
     * @param \Peakk\Threadflo\Helper\Data $helper
     * @param \Peakk\Threadflo\Model\ItemFactory $itemFactory
     * @param \Peakk\Threadflo\Model\Item\ImageFactory $itemImageFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     */
    public function __construct(
        \Peakk\Threadflo\Helper\Data $helper,
        \Peakk\Threadflo\Model\ItemFactory $itemFactory,
        \Peakk\Threadflo\Model\Item\ImageFactory $itemImageFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Directory\Model\RegionFactory $regionFactory
    ) {
        $this->_helper = $helper;
        $this->_itemFactory = $itemFactory;
        $this->_itemImageFactory = $itemImageFactory;
        $this->_orderFactory = $orderFactory;
        $this->_regionFactory = $regionFactory;
    }
    
    /**
     * Send request to Threadflo API.
     * 
     * @param string $apiUrl
     * @param string $request
     * @return bool
     */
    protected function send($apiUrl, $request = null)
    {
        if (!$this->_helper->isApiEnabled()) {
            $this->_helper->logError('API Request Error: API disabled.');

            return false;
        } elseif ($this->_helper->isApiConfigured()) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-API-KEY:'.$this->_helper->getApiKey()));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            if ($request) {
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            }

            if (curl_errno($ch)) {
                $this->_helper->logError('System Error: curl init error.');
            }

            $response = curl_exec($ch);

            curl_close($ch);

            $responseXml = strpos($response, 'xml version=') > 0 ? simplexml_load_string($response) : json_decode($response);

            if ($response) {
                if ($responseXml && $responseXml->error) {
                    $this->_helper->logError('API Response Error: '.$responseXml->error);
                } else {
                    return $responseXml;
                }
            } else {
                $this->_helper->logError('API Response Error: API connection failed.');
            }
        } else {
            $this->_helper->logError('API Error: API not configured.');
        }

        return false;
    }

}