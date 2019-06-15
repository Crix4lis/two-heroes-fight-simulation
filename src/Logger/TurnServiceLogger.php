<?php
declare(strict_types=1);

namespace Emagia\Logger;

use Emagia\TurnServiceInterface;
use Emagia\Unit\UnitInterface;
use LogicException;
use Monolog\Logger as MonoLogger;

class TurnServiceLogger extends BaseLogger implements TurnServiceInterface
{
    /**
     * @var \Emagia\TurnServiceInterface
     */
    private $service;

    public function __construct(
        TurnServiceInterface $service,
        MonoLogger $logger,
        int $level = MonoLogger::CRITICAL,
        string $logFilePath = BaseLogger::DEFAULT_FILE
    )
    {
        parent::__construct($logger, $level, $logFilePath);
        $this->service = $service;
    }

    /**
     * @param UnitInterface $attacker
     * @param UnitInterface $defender
     *
     * @throws LogicException
     */
    public function make(UnitInterface $attacker, UnitInterface $defender): void
    {
        try {
            $this->service->make($attacker, $defender);
        } catch (LogicException $e) {
            $msg = sprintf(
                'Attacker %s is dead! That must not happen. Make sure you didn\'t brake the application',
                $attacker->getName()
            );
            $att = var_export($attacker, true);
            $deff = var_export($defender, true);

            $this->logger->critical($msg);
            $this->logger->critical('Attacker dump: ' . $att);
            $this->logger->critical('Defender dump: ' . $deff);
            throw $e;
        }
    }
}
