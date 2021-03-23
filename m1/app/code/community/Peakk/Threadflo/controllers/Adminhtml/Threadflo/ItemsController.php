<?php

class Peakk_Threadflo_Adminhtml_Threadflo_ItemsController extends Mage_Adminhtml_Controller_Action
{
    
    /**
     * List imported Threadflo design items. 
     */
    public function indexAction()
    {
        $helper = Mage::helper('threadflo');
        $helper->logTransaction('source: admin:indexAction:start');

        $this->loadLayout()->_setActiveMenu('catalog');

        $cssBlock = $this->getLayout()->createBlock('adminhtml/template')->setTemplate('threadflo/css.phtml');
        $headerBlock = $this->getLayout()->createBlock('adminhtml/template')->setTemplate('threadflo/items/head.phtml');
        $listBlock = $this->getLayout()->createBlock('threadflo/adminhtml_items_list')->setTemplate('threadflo/items/list.phtml');

        $this->getLayout()->getBlock('content')->append($cssBlock);
        $this->getLayout()->getBlock('content')->append($headerBlock);
        $this->getLayout()->getBlock('content')->append($listBlock);

        $this->renderLayout();

        $helper->logTransaction('source: admin:indexAction:end');
    }
    
    /**
     * Import design items.
     */
    public function importPostAction()
    {
        $helper = Mage::helper('threadflo');
        $helper->logTransaction('source: admin:importPost:start');

        set_time_limit(0);

        $api = Mage::getModel('threadflo/api_products');

        $response = $api->importItems();

        if ($response) {
            $this->_getSession()->addSuccess('Import successful.');
        } else {
            $this->_getSession()->addError('Import failed. Please check API configuration, API availability, and server settings.');
        }

        $helper->logTransaction('source: admin:importPost:end');

        $this->_redirect('*/*/index/', array('_secure' => true));
    }
    
    /**
     * Update product catalog.
     */
    public function createProductsPostAction()
    {
        $helper = Mage::helper('threadflo');
        $helper->logTransaction('source: admin:createProductsPostAction:start');

        set_time_limit(0);

        Mage::getModel('threadflo/processor_catalog')->createProducts();

        $this->_getSession()->addSuccess('Products updated.');

        $helper->logTransaction('source: admin:importPost:end');

        $this->_redirect('*/catalog_product/index/', array('_secure' => true));
    }

}