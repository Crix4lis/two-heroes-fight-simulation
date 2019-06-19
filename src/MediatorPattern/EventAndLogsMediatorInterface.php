<?php
declare(strict_types=1);

namespace Emagia\MediatorPattern;

use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Luck;
use Emagia\Property\Speed;
use Emagia\Property\Strength;
use Emagia\Unit\UnitInterface;

interface EventAndLogsMediatorInterface
{
    public function throwBlockedDamageEvent(string $defenderName, int $blockedDamage): void;
    public function throwDefenderAlreadyDeadEvent(UnitInterface $attacker, UnitInterface $deadUnit): void;
    public function throwGameFinishedWithoutWinnerEvent(
        string $firstUnitName,
        HealthPoints $fistUnitHp,
        string $secondUnitName,
        HealthPoints $secondUnitHp,
        int $turn
    ): void;
    public function throwGameFinishedWithWinner(string $winnerName, HealthPoints $winnerHp, int $turn): void;
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
    ): void;
    public function throwMagicShieldUsedEvent(string $defenderName, int $damageReducedTo): void;
    public function throwPerformedAttackEvent(string $attackerName, int $attackDmg): void;
    public function throwRapidStrikeUsedEvent(string $attackerName): void;
    public function throwReceivedDamageEvent(string $defenderName, int $receivedDamage, int $defenderHpLeft): void;
    public function throwTurnStartsEvent(string $attackerName, string $defenderName, int $turn): void;
    public function throwUnitDiedEvent(string $unitName): void;
    public function logErrorCannotResolveAttacker(): void;
    public function logCriticalAttackerIsDead(UnitInterface $attacker, UnitInterface $defender): void;
}
