<?php

namespace Peakk\Threadflo\Controller\Adminhtml\Designs;

use Symfony\Component\Config\Definition\Exception\Exception;

class CreateProductsPost extends \Magento\Backend\App\Action
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
     * @var \Peakk\Threadflo\Model\Processor
     */
    protected $_processor;

    /**
     * @var \Peakk\Threadflo\Helper\Data
     */
    protected $_helper;

    /**
     * CreateProductsPost constructor.
     * 
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory
     * @param \Magento\Framework\UrlInterface $url
     * @param \Peakk\Threadflo\Model\Processor $processor
     * @param \Peakk\Threadflo\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Controller\Result\RedirectFactory $redirectFactory,
        \Magento\Framework\UrlInterface $url,
        \Peakk\Threadflo\Model\Processor $processor,
        \Peakk\Threadflo\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->_pageFactory = $pageFactory;
        $this->_messageManager = $messageManager;
        $this->_redirectFactory = $redirectFactory;
        $this->_url = $url;
        $this->_processor = $processor;
        $this->_helper = $helper;
    }
    
    /**
     * Create or update Threadflo products.
     * 
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $this->_helper->logTransaction('source: admin:createProductsPost:start');

        set_time_limit(0);

        try {
            $response = $this->_processor->createProducts();

            if ($response) {
                $this->_messageManager->addSuccess('Products created.');

                $resultRedirect = $this->_redirectFactory->create();
                return $resultRedirect->setUrl($this->_url->getUrl('catalog/product/index'));
            } else {
                $this->_messageManager->addError('Error creating products.');
            }
        } catch (Exception $e) {
            $this->_messageManager->addError('Error creating products: '.$e->getMessage());
        }

        $resultRedirect = $this->_redirectFactory->create();

        $this->_helper->logTransaction('source: admin:createProductsPost:end');

        return $resultRedirect->setUrl($this->_url->getUrl('*/*/listall'));
    }

}