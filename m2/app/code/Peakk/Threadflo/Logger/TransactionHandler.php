<?php

namespace Peakk\Threadflo\Logger;

class TransactionHandler extends \Magento\Framework\Logger\Handler\Base
{

    /**
     * @var int
     */
    protected $loggerType = \Monolog\Logger::DEBUG;

    /**
     * @var string
     */
    protected $fileName = '/var/log/threadflo_transactions.log';

}