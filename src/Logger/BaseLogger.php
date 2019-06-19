<?php
declare(strict_types=1);

namespace Emagia\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Logger as MonoLogger;

abstract class BaseLogger
{
    /** @var \Monolog\Logger */
    protected $logger;
    /** @var int */
    protected $level;
    public const DEFAULT_FILE = 'logs.log';

    public function __construct(
        MonoLogger $logger,
        int $level = MonoLogger::CRITICAL,
        string $logFilePath = self::DEFAULT_FILE
    ) {
        $this->logger = $logger;
        $this->level = $level;

        try {
            $this->logger->pushHandler(new StreamHandler($logFilePath, $level));
        } catch (\Exception $e) {
            echo "\nCannot access log file. Create 'logs.log' file in your project root and set permissions!";
        }
    }
}
