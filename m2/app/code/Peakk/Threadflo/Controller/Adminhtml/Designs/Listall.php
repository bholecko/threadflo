<?php

namespace Peakk\Threadflo\Controller\Adminhtml\Designs;

class Listall extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_resultPageFactory;

    /**
     * @var \Peakk\Threadflo\Helper\Data
     */
    protected $_helper;

    /**
     * Listall constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Peakk\Threadflo\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Peakk\Threadflo\Helper\Data $helper
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
        parent::__construct($context);
    }

    /**
     * List all Threadflo items.
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->_helper->logTransaction('source: admin:listAll:start');

        $resultPage = $this->_resultPageFactory->create();

        $resultPage->setActiveMenu('Magento_Backend::content');

        $this->_helper->logTransaction('source: admin:listAll:end');

        return $resultPage;
    }

}