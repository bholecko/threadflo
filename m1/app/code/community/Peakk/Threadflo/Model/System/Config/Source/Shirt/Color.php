<?php

class Peakk_Threadflo_Model_System_Config_Source_Shirt_Color
{

    /**
     * @var array
     */
    protected $_options;

    /**
     * Return Threadflo shirt color labels.
     *
     * @return array
     */
    public function toAttributeArray()
    {
        if (!$this->_options) {
            $options = array(
                0 => 'None',
                1 => 'Aqua',
                2 => 'Army',
                3 => 'Ash',
                4 => 'Ash Grey Sea Foam',
                5 => 'Asphalt',
                6 => 'Athletic Heather',
                7 => 'Baby Blue',
                8 => 'Banana',
                9 => 'Banana Cream',
                10 => 'Black',
                11 => 'Brown',
                12 => 'Brown Heather',
                13 => 'Burgundy',
                14 => 'Cancun',
                15 => 'Cardinal',
                16 => 'Carolina Blue',
                17 => 'Celadon',
                18 => 'Charcoal',
                19 => 'Charcoal Heather',
                20 => 'Coffee',
                21 => 'Cool Blue',
                22 => 'Cranberry',
                23 => 'Cream',
                24 => 'Creme',
                25 => 'Dark Chocolate',
                26 => 'Dark Grey',
                27 => 'Denim Heather',
                28 => 'Eggplant',
                29 => 'Forest',
                30 => 'Forest Green',
                31 => 'Fuchsia',
                32 => 'Fuschia',
                33 => 'Gold',
                34 => 'Grass',
                35 => 'Harbor Blue',
                36 => 'Heather Grey',
                37 => 'Heavy Metal',
                38 => 'Hot Pink',
                39 => 'Hunter Green',
                40 => 'Indigo',
                41 => 'Kelly',
                42 => 'Kelly Green',
                43 => 'Khaki',
                44 => 'Lapis',
                45 => 'Lavender',
                46 => 'Lemon',
                47 => 'Light Aqua',
                48 => 'Light Blue',
                49 => 'Light Gray',
                50 => 'Light Olive',
                51 => 'Light Orange',
                52 => 'Light Pink',
                53 => 'Lilac',
                54 => 'Lime',
                55 => 'Maroon',
                56 => 'Midnight Navy',
                57 => 'Military Green',
                58 => 'Mint',
                59 => 'Mustard',
                60 => 'Natural',
                61 => 'Navy',
                62 => 'Neon Green',
                63 => 'New Silver',
                64 => 'Oatmeal',
                65 => 'Olive',
                66 => 'Orange',
                67 => 'Pink',
                68 => 'Plum',
                69 => 'Powder Blue',
                70 => 'Purple',
                71 => 'Purple Rush',
                72 => 'Raspberry',
                73 => 'Red',
                74 => 'Royal',
                75 => 'Royal Blue',
                76 => 'Safari Green',
                77 => 'Safety Green',
                78 => 'Sand',
                79 => 'Scarlet',
                80 => 'Sea Foam',
                81 => 'Seafoam',
                82 => 'Shocking Pink',
                83 => 'Silver',
                84 => 'Slate',
                85 => 'Sunshine',
                86 => 'Tahiti Blue',
                87 => 'Tar',
                88 => 'Teal',
                89 => 'Texas Orange',
                90 => 'Turquoise',
                91 => 'Warm Gray',
                92 => 'White',
                93 => 'Yellow'
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