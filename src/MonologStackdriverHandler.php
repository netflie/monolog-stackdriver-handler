<?php declare(strict_types=1);

namespace Netflie\MonologStackdriverHandler;

use Google\Cloud\Logging\LoggingClient;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class MonologStackdriverHandler extends AbstractProcessingHandler
{
    /**
     * The Stackdriver logger
     * @var Google\Cloud\Logging\PsrLogger
     */
    private $logger;

    /**
     * @param string  $logName              The name of the log to write entries to.
     * @param array   $loggingClientOptions Google\Cloud\Logging\LoggingClient options
     * @param array   $loggerOptions        Google\Cloud\Logging\LoggingClient::psrLogger options
     * @param int     $level                The minimum logging level at which this handler will be triggered
     * @param Boolean $bubble               Whether the messages that are handled can bubble up the stack or not
     * @param Boolean $loggingClient        For testing purposes
     */
    public function __construct(
        string $logName,
        array $loggingClientOptions = [],
        array $loggerOptions = [],
        int $level = Logger::NOTICE,
        bool $bubble = true,
        LoggingClient $loggingClient = null
    ) {
        parent::__construct($level, $bubble);

        $loggingClient = $loggingClient ?? new LoggingClient($loggingClientOptions);
        $this->logger = $loggingClient->psrLogger($logName, $loggerOptions);
        $this->level = $level;
        $this->bubble = $bubble;
    }

    public function write(array $record): void
    {
        $this->logger->log(
            $record['level_name'],
            $record['formatted'],
            $record['context']
        );
    }
}