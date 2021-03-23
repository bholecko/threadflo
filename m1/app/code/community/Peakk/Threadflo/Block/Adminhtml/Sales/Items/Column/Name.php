<?php

class Peakk_Threadflo_Block_Adminhtml_Sales_Items_Column_Name extends Mage_Adminhtml_Block_Sales_Items_Column_Name
{

    /**
     * Add Threadflo options to the other product options.
     *
     * @return array
     */
    public function getOrderOptions()
    {
        $orderOptions = parent::getOrderOptions();

        if ($this->getItem()->getThreadfloItemId()) {
            $orderOption = array(
                array(
                    'label' => 'Threadflo Item ID',
                    'value' => $this->getItem()->getThreadfloItemId()
                ),
                array(
                    'label' => 'Threadflo Item SKU',
                    'value' => $this->getItem()->getThreadfloItemSku()
                )
            );

            return $orderOptions ? array_merge($orderOption, $orderOptions) : $orderOption;
        } else {
            return $orderOptions;
        }
    }

}

