<?php
declare(strict_types=1);

namespace Emagia;

use Emagia\Logger\ErrorLoggerInterface;
use Emagia\Logger\GameplayLoggerInterface;
use Emagia\MediatorPattern\ColleagueInterface;
use Emagia\MediatorPattern\EventAndLogsMediator;
use Emagia\MediatorPattern\EventAndLogsMediatorInterface;
use Emagia\Reader\GameReaderInterface;

class MediatorFactory
{
    /** @var GameplayLoggerInterface */
    private $gameplayLogger;
    /** @var GameReaderInterface */
    private $gameplayReader;
    /** @var ErrorLoggerInterface */
    private $errorLogger;

    public function __construct(
        GameplayLoggerInterface $gameplayLogger,
        GameReaderInterface $gameplayReader,
        ErrorLoggerInterface $errorLogger
    ) {
        $this->gameplayLogger = $gameplayLogger;
        $this->gameplayReader = $gameplayReader;
        $this->errorLogger = $errorLogger;
    }

    public function createMediatorForColleagues(
        ColleagueInterface $firstUnit,
        ColleagueInterface $secondUnit,
        ColleagueInterface $attackerResolver,
        ColleagueInterface $turnService
    ): EventAndLogsMediatorInterface
    {
        return new EventAndLogsMediator(
            $firstUnit,
            $secondUnit,
            $attackerResolver,
            $turnService,
            $this->gameplayLogger,
            $this->gameplayReader,
            $this->errorLogger
        );
    }
}
