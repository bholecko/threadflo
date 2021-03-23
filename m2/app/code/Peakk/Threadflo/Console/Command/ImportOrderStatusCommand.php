<?php

namespace Peakk\Threadflo\Console\Command;

class ImportOrderStatusCommand extends \Symfony\Component\Console\Command
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
     * ImportOrderStatusCommand constructor.
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
        $this->setName('threadflo:import:order_status')->setDescription('Imports Threadflo order status for all Threadflo orders.');

        parent::configure();
    }
    
    /**
     * Import order status values for threadflo_order_status.
     * 
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_helper->logTransaction('source: shell:importOrderStatus:start');

        $output->writeln('<info>Importing/syncing designs...</info>');

        $this->_objectManager->get('Peakk\Threadflo\Model\Api\Orders')->importOrderStatus();

        $output->writeln('<info>Designs imported/synced.</info>');

        $this->_helper->logTransaction('source: shell:importOrderStatus:end');
    }

}