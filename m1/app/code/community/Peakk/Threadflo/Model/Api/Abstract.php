<?php

class Peakk_Threadflo_Model_Api_Abstract
{

    /**
     * Threadflo API URL constants.
     */
    const API_CONNECTOR_URL = 'https://login.threadflo.com/api/connector/';

    /**
     * Send request to Threadflo API
     * 
     * @param $apiUrl
     * @param string $request
     * @return bool|mixed|SimpleXMLElement
     */
    protected function send($apiUrl, $request = null)
    {
        $helper = Mage::helper('threadflo');

        if (!$helper->isApiEnabled()) {
            $helper->logError('API Request Error: API disabled.');

            return false;
        } elseif ($helper->isApiConfigured()) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-API-KEY:'.$helper->getApiKey()));
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            if ($request) {
                curl_setopt($ch, CURLOPT_HEADER, false);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
            }

            if (curl_errno($ch)) {
                $helper->logError('System Error: curl init error.');
            }

            $response = curl_exec($ch);

            curl_close($ch);

            $responseXml = strpos($response, 'xml version=') > 0 ? simplexml_load_string($response) : json_decode($response);

            if ($response) {
                if ($responseXml && $responseXml->error) {
                    $helper->logError('API Response Error: '.$responseXml->error);
                } else {
                    return $responseXml;
                }
            } else {
                $helper->logError('API Response Error: API connection failed.');
            }
        } else {
            $helper->logError('API Error: API not configured.');
        }

        return false;
    }

}