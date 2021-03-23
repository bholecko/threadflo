<?php

namespace Peakk\Threadflo\Console\Command;

use Magento\TestFramework\ObjectManagerFactory;

class UpdateProductsCommand extends \Symfony\Component\Console\Command
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
     * UpdateProductsCommand constructor.
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
        $this->setName('threadflo:update:products')->setDescription('Creates/updates Threadflo products using imported designs.');

        parent::configure();
    }

    /**
     * Create or update Threadflo product catalog.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_helper->logTransaction('source: shell:updateProducts:start');

        $output->writeln('<info>Creating/updating products...</info>');

        $this->_objectManager->get('Peakk\Threadflo\Model\Processor\Catalog')->createProducts();

        $output->writeln('<info>Products created/updated.</info>');

        $this->_helper->logTransaction('source: shell:updateProducts:end');
    }

}