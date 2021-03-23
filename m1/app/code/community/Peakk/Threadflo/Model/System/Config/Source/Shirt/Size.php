<?php

class Peakk_Threadflo_Model_System_Config_Source_Shirt_Size
{

    /**
     * @var array
     */
    protected $_options;

    /**
     * Return Threadflo shirt size labels.
     *
     * @return array
     */
    public function toAttributeArray()
    {
        if (!$this->_options) {
            $options = array (
                0 => 'None',
                1 => 'X-Small',
                2 => 'Small',
                3 => 'Medium',
                4 => 'Large',
                5 => 'X-Large',
                6 => '2X-Large',
                7 => '3X-Large',
                8 => '4X-Large',
                9 => '5X-Large',
                10 => '6X-Large'
            );

            $this->_options = $options;
        }

        return $this->_options;
    }

    /**
     * Return labels without key values.
     *
     * @return array
     */
    public function getOption()
    {
        $option = array();

        foreach ($this->getAllOptions() as $id => $label) {
            $option[] = $label;
        }

        return $option;
    }

}