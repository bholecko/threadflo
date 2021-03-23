<?php

require_once '../abstract.php';

class Mage_Shell_Threadflo_Update_Products extends Mage_Shell_Abstract
{

    public function run()
    {
        $this->_helper->logTransaction('source: shell:updateProducts:start');

        echo 'Creating/updating products...'."\n";

        Mage::getModel('threadflo/processor_catalog')->createProducts();

        echo 'Products created/updated.'."\n";

        $this->_helper->logTransaction('source: shell:updateProducts:end');
    }

}

set_time_limit(0);
$shell = new Mage_Shell_Threadflo_Update_Products();
$shell->run();
