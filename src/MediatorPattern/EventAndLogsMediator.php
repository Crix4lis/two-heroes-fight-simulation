<?php
declare(strict_types=1);

namespace Emagia\MediatorPattern;

use Emagia\Event\BlockedDamageEvent;
use Emagia\Event\DefenderAlredyDeadEvent;
use Emagia\Event\GameFinishedWithoutWinnerEvent;
use Emagia\Event\GameFinishedWithWinner;
use Emagia\Event\GameStartedEvent;
use Emagia\Event\MagicShieldUsedEvent;
use Emagia\Event\PerformedAttackEvent;
use Emagia\Event\RapidStrikeUsedEvent;
use Emagia\Event\ReceivedDamageEvent;
use Emagia\Event\TurnStartsEvent;
use Emagia\Event\UnitDiedEvent;
use Emagia\Logger\ErrorLoggerInterface;
use Emagia\Logger\GameplayLoggerInterface;
use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Luck;
use Emagia\Property\Speed;
use Emagia\Property\Strength;
use Emagia\Reader\GameReaderInterface;
use Emagia\Unit\UnitInterface;

/**
 * It looks like a facade and I guess facade would be enough in this use case because
 * there is no need for bi-directional communication between Colleagues
 */
class EventAndLogsMediator implements EventAndLogsMediatorInterface
{
    /** @var GameplayLoggerInterface */
    private $gameplayLogger;
    /** @var GameReaderInterface */
    private $gameplayReader;
    /** @var ErrorLoggerInterface */
    private $errorLogger;

    public function __construct(
        ColleagueInterface $firstUnit,
        ColleagueInterface $secondUnit,
        ColleagueInterface $attackerResolver,
        ColleagueInterface $turnService,
        GameplayLoggerInterface $gameplayLogger,
        GameReaderInterface $gameplayReader,
        ErrorLoggerInterface $errorLogger
    ) {
        $firstUnit->setMediator($this);
        $secondUnit->setMediator($this);
        $attackerResolver->setMediator($this);
        $turnService->setMediator($this);

        $this->gameplayLogger = $gameplayLogger;
        $this->gameplayReader = $gameplayReader;
        $this->errorLogger = $errorLogger;
    }

    public function throwBlockedDamageEvent(string $defenderName, int $blockedDamage): void
    {
        $event = new BlockedDamageEvent($defenderName, $blockedDamage);
        $this->gameplayLogger->logEvent($event);
        $this->gameplayReader->printEvent($event);
    }

    public function throwDefenderAlreadyDeadEvent(UnitInterface $attacker, UnitInterface $deadUnit): void
    {
        $event = new DefenderAlredyDeadEvent($attacker, $deadUnit);
        $this->gameplayLogger->logEvent($event);
        $this->gameplayReader->printEvent($event);
    }

    public function throwGameFinishedWithoutWinnerEvent(
        string $firstUnitName,
        HealthPoints $fistUnitHp,
        string $secondUnitName,
        HealthPoints $secondUnitHp,
        int $turn
    ): void
    {
        $event = new GameFinishedWithoutWinnerEvent(
            $firstUnitName,
            $fistUnitHp,
            $secondUnitName,
            $secondUnitHp,
            $turn
        );
        $this->gameplayLogger->logEvent($event);
        $this->gameplayReader->printEvent($event);
    }

    public function throwGameFinishedWithWinner(string $winnerName, HealthPoints $winnerHp, int $turn): void
    {
        $event = new GameFinishedWithWinner($winnerName, $winnerHp, $turn);
        $this->gameplayLogger->logEvent($event);
        $this->gameplayReader->printEvent($event);
    }

    public function throwGameStartedEvent(
        string $attackerName,
        HealthPoints $attackerHealth,
        Strength $attackerStrength,
        Defence $attackerDefense,
        Speed $attackerSpeed,
        Luck $attackerLuck,
        string $defenderName,
        HealthPoints $defenderHealth,
        Strength $defenderStrength,
        Defence $defenderDefense,
        Speed $defenderSpeed,
        Luck $defenderLuck,
        int $maxRounds
    ): void
    {
        $event = new GameStartedEvent(
            $attackerName,
            $attackerHealth,
            $attackerStrength,
            $attackerDefense,
            $attackerSpeed,
            $attackerLuck,
            $defenderName,
            $defenderHealth,
            $defenderStrength,
            $defenderDefense,
            $defenderSpeed,
            $defenderLuck,
            $maxRounds
        );
        $this->gameplayLogger->logEvent($event);
        $this->gameplayReader->printEvent($event);
    }

    public function throwMagicShieldUsedEvent(string $defenderName, int $damageReducedTo): void
    {
        $event = new MagicShieldUsedEvent($defenderName, $damageReducedTo);
        $this->gameplayLogger->logEvent($event);
        $this->gameplayReader->printEvent($event);
    }

    public function throwPerformedAttackEvent(string $attackerName, int $attackDmg): void
    {
        $event = new PerformedAttackEvent($attackerName, $attackDmg);
        $this->gameplayLogger->logEvent($event);
        $this->gameplayReader->printEvent($event);
    }

    public function throwRapidStrikeUsedEvent(string $attackerName): void
    {
        $event = new RapidStrikeUsedEvent($attackerName);
        $this->gameplayLogger->logEvent($event);
        $this->gameplayReader->printEvent($event);
    }

    public function throwReceivedDamageEvent(string $defenderName, int $receivedDamage, int $defenderHpLeft): void
    {
        $event = new ReceivedDamageEvent($defenderName, $receivedDamage, $defenderHpLeft);
        $this->gameplayLogger->logEvent($event);
        $this->gameplayReader->printEvent($event);
    }

    public function throwTurnStartsEvent(string $attackerName, string $defenderName, int $turn): void
    {
        $event = new TurnStartsEvent($attackerName, $defenderName, $turn);
        $this->gameplayLogger->logEvent($event);
        $this->gameplayReader->printEvent($event);
    }

    public function throwUnitDiedEvent(string $unitName): void
    {
        $event = new UnitDiedEvent($unitName);
        $this->gameplayLogger->logEvent($event);
        $this->gameplayReader->printEvent($event);
    }

    public function logErrorCannotResolveAttacker(): void
    {
        $this->errorLogger->logErrorCannotResolveAttacker();
    }

    public function logCriticalAttackerIsDead(UnitInterface $attacker, UnitInterface $defender): void
    {
        $this->errorLogger->logCriticalAttackerIsDead($attacker, $defender);
    }
}
