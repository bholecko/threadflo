<?php

require_once '../abstract.php';

class Mage_Shell_Threadflo_Import_Shipping_Tracking extends Mage_Shell_Abstract
{

    public function run()
    {
        $this->_helper->logTransaction('source: shell:importShippingTracking:start');

        echo 'Importing Threadflo shipping tracking data...'."\n";

        Mage::getModel('threadflo/api')->importShippingTracking();

        echo 'Threadflo shipping tracking data updated.'."\n";

        $this->_helper->logTransaction('source: shell:importShippingTracking:end');
    }

}

set_time_limit(0);
$shell = new Mage_Shell_Threadflo_Import_Shipping_Tracking();
$shell->run();
