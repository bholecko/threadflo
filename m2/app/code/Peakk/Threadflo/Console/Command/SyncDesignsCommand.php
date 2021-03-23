<?php

namespace Peakk\Threadflo\Console\Command;

class SyncDesignsCommand extends \Symfony\Component\Console\Command
{

    /**
     * @var ObjectManagerFactory
     */
    protected $_objectManager;

    /**
     * @var \Peakk\Threadflo\Helper\Data
     */
    protected $_helper;

    /**
     * SyncDesignsCommand constructor.
     *
     * @param ObjectManagerFactory $objectManagerFactory
     * @param \Peakk\Threadflo\Helper\Data $helper
     */
    public function __construct(
        ObjectManagerFactory $objectManagerFactory,
        \Peakk\Threadflo\Helper\Data $helper
    ) {
        $params = $_SERVER;

        $params[StoreManager::PARAM_RUN_CODE] = 'admin';
        $params[StoreManager::PARAM_RUN_TYPE] = 'store';

        $this->_objectManager = $objectManagerFactory->create($params);
        $this->_helper = $helper;

        parent::__construct();
    }

    /**
     * Configure command.
     */
    protected function configure()
    {
        $this->setName('threadflo:sync:designs')->setDescription('Imports/syncs Threadflo designs.');
        
        parent::configure();
    }

    /**
     * Import product items.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_helper->logTransaction('source: shell:syncDesigns:start');

        $output->writeln('<info>Importing/syncing designs...</info>');

        $this->_objectManager->get('Peakk\Threadflo\Model\Api\Products')->importItems();

        $output->writeln('<info>Designs imported/synced.</info>');

        $this->_helper->logTransaction('source: shell:syncDesigns:end');
    }

}