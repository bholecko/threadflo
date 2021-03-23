<?php

namespace Peakk\Threadflo\Logger;

class ErrorHandler extends \Magento\Framework\Logger\Handler\Base
{

    /**
     * @var int
     */
    protected $loggerType = \Monolog\Logger::ERROR;

    /**
     * @var string
     */
    protected $fileName = '/var/log/threadflo_error.log';

}