<?php

namespace Peakk\Threadflo\Block\Adminhtml\Sales\Order\View\Tab;

class Info extends \Magento\Sales\Block\Adminhtml\Order\View\Tab\Info
{

    /**
     * Return custom tab label.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Threadflo');
    }

}