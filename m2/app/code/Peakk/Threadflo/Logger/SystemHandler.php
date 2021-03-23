<?php

namespace Peakk\Threadflo\Logger;

class SystemHandler extends \Magento\Framework\Logger\Handler\Base
{

    /**
     * @var int
     */
    protected $loggerType = \Monolog\Logger::DEBUG;

    /**
     * @var string
     */
    protected $fileName = '/var/log/threadflo_system.log';

}