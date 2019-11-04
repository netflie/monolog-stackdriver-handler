<?php declare(strict_types=1);

namespace Netflie\MonologStackdriverHandler;

use PHPUnit\Framework\TestCase;

class MonologStackdriverHandlerTest extends TestCase
{
    public function testSendStackdriverRequest(): void
    {
        $handler = $this->createHandler();
        $handler->handle($this->getRecord());
    }

    private function createHandler()
    {
        $logName = 'fake_log_name';

        $psrLogger = $this->prophesize(\Google\Cloud\Logging\PsrLogger::class);
        $loggingClientprophesize = $this->prophesize(\Google\Cloud\Logging\LoggingClient::class);
        $loggingClientprophesize->psrLogger($logName, [])->willReturn($psrLogger->reveal());
        $loggingClient = $loggingClientprophesize->reveal();

        $handler = new MonologStackdriverHandler(
            $logName,
            [],
            [],
            \Monolog\Logger::ERROR,
            true,
            $loggingClient
        );

        $logRecord = $this->getRecord();
        $logRecord['formatted'] = $handler->getFormatter()->format($logRecord);
        $psrLogger
            ->log($logRecord['level_name'], $logRecord['formatted'], $logRecord['context'])
            ->shouldBeCalled();

        return $handler;
    }

    /**
     * @return array Record
     */
    private function getRecord(
        $level = \Monolog\Logger::ERROR,
        $message = 'test',
        array $context = []
    ): array {
        static $record = null;

        if ($record) {
            return $record;
        }

        $record = [
            'message' => (string) $message,
            'context' => $context,
            'level' => $level,
            'level_name' => \Monolog\Logger::getLevelName($level),
            'channel' => 'test',
            'datetime' => new \Monolog\DateTimeImmutable(true),
            'extra' => [],
        ];

        return $record;
    }
}