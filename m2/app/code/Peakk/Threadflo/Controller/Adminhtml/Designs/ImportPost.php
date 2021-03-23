<?php

namespace Peakk\Threadflo\Controller\Adminhtml\Designs;

class ImportPost extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $_redirectFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @var \Peakk\Threadflo\Model\Api
     */
    protected $_api;

    /**
     * @var \Peakk\Threadflo\Helper\Data
     */
    protected $_helper;

    /**
     * ImportPost constructor.
     * 
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param \Magento\Framework\UrlInterface $url
     * @param \Peakk\Threadflo\Model\Api $api
     * @param \Peakk\Threadflo\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
        \Magento\Framework\UrlInterface $url,
        \Peakk\Threadflo\Model\Api $api,
        \Peakk\Threadflo\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->_pageFactory = $pageFactory;
        $this->_messageManager = $messageManager;
        $this->_redirectFactory = $redirectFactory;
        $this->_url = $url;
        $this->_api = $api;
        $this->_helper = $helper;
    }
    
    /**
     * Sync designs.
     * 
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $this->_helper->logTransaction('source: admin:importPost:start');

        set_time_limit(0);

        $response = $this->_api->importItems();

        if ($response) {
            $this->_messageManager->addSuccess('Import successful.');
        } else {
            $this->_messageManager->addError('Import failed. Please check API configuration, API availability, and server settings.');
        }

        $resultRedirect = $this->_redirectFactory->create();

        $this->_helper->logTransaction('source: admin:importPost:end');

        return $resultRedirect->setUrl($this->_url->getUrl('*/*/listall'));
    }

}