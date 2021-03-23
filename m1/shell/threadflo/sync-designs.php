<?php

require_once '../abstract.php';

class Mage_Shell_Threadflo_Sync_Designs extends Mage_Shell_Abstract
{

    public function run()
    {
        $this->_helper->logTransaction('source: shell:syncDesigns:start');

        echo 'Importing/syncing designs...'."\n";

        Mage::getModel('threadflo/api')->importItems();

        echo 'Designs imported/synced.'."\n";

        $this->_helper->logTransaction('source: shell:syncDesigns:end');
    }

}

set_time_limit(0);
$shell = new Mage_Shell_Threadflo_Sync_Designs();
$shell->run();
