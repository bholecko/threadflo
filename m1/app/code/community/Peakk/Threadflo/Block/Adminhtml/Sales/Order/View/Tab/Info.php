<?php

class Peakk_Threadflo_Block_Adminhtml_Sales_Order_View_Tab_Info extends Mage_Adminhtml_Block_Sales_Order_View_Tab_Info
{

    /**
     * Return custom tab label.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('threadflo')->__('Threadflo');
    }

}