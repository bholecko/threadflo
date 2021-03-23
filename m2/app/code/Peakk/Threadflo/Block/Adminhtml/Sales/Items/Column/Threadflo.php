<?php

namespace Peakk\Threadflo\Block\Adminhtml\Sales\Items\Column;

class Threadflo extends \Magento\Framework\View\Element\Template
{

    /**
     * Return block HTML.
     * 
     * @return string
     */
    public function toHtml()
    {
        $html = '';

        $html .= '<div><strong>Threadflo Item ID:</strong> '.$this->getItem()->getThreadfloItemId().'</div>';
        $html .= '<div><strong>Threadflo Item SKU:</strong> '.$this->getItem()->getThreadfloItemSku().'</div>';

        return $html;
    }

}

