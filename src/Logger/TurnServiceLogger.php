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
        int $level = MonoLogger::ERROR,
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
            $msg = $e->getMessage();
            $att = var_export($attacker, true);
            $deff = var_export($defender, true);

            $this->logger->error($msg);
            $this->logger->error($att);
            $this->logger->error($deff);
            throw $e;
        }
    }
}
