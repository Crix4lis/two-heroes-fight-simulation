<?php
declare(strict_types=1);

namespace Emagia\MediatorPattern;

interface MediatorInterface
{
    public function throwBlockedDamageEvent(): void;
    public function throwDefenderAlreadyDeadEvent(): void;
    public function throwGameFinishedWithoutWinnerEvent(): void;
    public function throwGameFinishedWithWinner(): void;
    public function throwGameStartedEvent(): void;
    public function throwMagicShieldUsedEvent(): void;
    public function throwPerformedAttackEvent(): void;
    public function throwRapidStrikeUsedEvent(): void;
    public function throwReceivedDamageEvent(): void;
    public function throwTurnStartsEvent(): void;
    public function throwUnitDiedEvent(): void;
    public function logErrorCannotResolveAttacker(): void;
    public function logCriticalAttackerIsDead(): void;
}
