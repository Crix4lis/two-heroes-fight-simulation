<?php
declare(strict_types=1);

namespace Emagia\Logger;

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
use Emagia\MediatorPattern\Event;
use Monolog\Logger as MonoLogger;

/**
 * Logs all events from Emagia\Event\
 */
class GameplayLogger extends BaseLogger implements GameplayLoggerInterface
{
    public function logEvent(Event $event): void
    {
        if ($this->level >= MonoLogger::DEBUG) {
            $msg = $this->prepareEventToLog($event);
            $msg === ''?: $this->logger->debug($msg);
        }

        if ($this->level >= MonoLogger::ERROR) {
            $error = $this->prepareErrorEventToLog($event);
            $error === ''?: $this->logger->error($error);
        }
    }

    private function prepareEventToLog(Event $event): string
    {
        if ($event instanceof GameStartedEvent) {
            $info = 'GAME STARTED! First attacker is: %s'
                .' with stats:['
                .'health points: %s'
                .' strength: %s'
                .' defence: %s'
                .' speed: %s'
                .' luck: %s]'
                .' First defender is: %s'
                .' with stats:['
                .'health points: %s'
                .' strength: %s'
                .' defence: %s'
                .' speed: %s'
                .' luck: %s]'
                .' max rounds are set to: %s';

            return sprintf(
                $info,
                $event->getAttackerName(),
                $event->getAttackerHealth(),
                $event->getAttackerStrength(),
                $event->getAttackerDefense(),
                $event->getAttackerSpeed(),
                $event->getAttackerLuck(),
                $event->getDefenderName(),
                $event->getDefenderHealth(),
                $event->getDefenderStrength(),
                $event->getDefenderDefense(),
                $event->getDefenderSpeed(),
                $event->getDefenderLuck(),
                $event->getMaxRounds()
            );
        }

        if ($event instanceof TurnStartsEvent) {
            return sprintf(
                'Turn no. %s. %s attacks %s',
                $event->getTurn(),
                ucfirst($event->getAttackerName()),
                ucfirst($event->getDefenderName())
            );
        }

        if ($event instanceof PerformedAttackEvent) {
            return sprintf(
                '%s attacked with %s dmg.',
                $event->getAttackerName(),
                $event->getAttackedWithDamage()
            );
        }

        if ($event instanceof RapidStrikeUsedEvent) {
            return sprintf(
                '%s uses rapid strike. Attacks again!',
                $event->getAttackerName()
            );
        }

        if ($event instanceof BlockedDamageEvent) {
            return sprintf(
                '%s blocked %s damage',
                $event->getDefenderName(),
                $event->getDamage()->getPoints()
            );
        }

        if ($event instanceof ReceivedDamageEvent) {
            return sprintf(
                '%s receives %s damage. %s hp left.',
                $event->getDefenderName(),
                $event->getReceivedDamage(),
                $event->getDefenderHpLeft()
            );
        }

        if ($event instanceof MagicShieldUsedEvent) {
            return sprintf(
                '%s uses magic shield! Damage to receive reduced to %s',
                $event->getDefenderName(),
                $event->getDamageReducedTo()
            );
        }

        if ($event instanceof UnitDiedEvent) {
            return sprintf(
                '%s died!',
                $event->getUnitName()
            );
        }

        if ($event instanceof GameFinishedWithWinner) {
            return sprintf(
                'Game over! Winner is: %s! %s has %s hp left',
                $event->getWinnerName(),
                $event->getWinnerName(),
                $event->getWinnerHp()
            );
        }

        if ($event instanceof GameFinishedWithoutWinnerEvent) {
            return sprintf(
                'Game over! Noone has won! First attacker %s has %s hp left. First defender %s has %s hp left.',
                $event->getFirstUnitName(),
                $event->getFirstUnitHp()->getPoints(),
                $event->getSecondUnitName(),
                $event->getSecondUnitHp()->getPoints()
            );
        }

        return '';
    }

    private function prepareErrorEventToLog(Event $event): string
    {
        if ($event instanceof DefenderAlredyDeadEvent) {
            return sprintf(
                'Defender named: %s is already dead! Attacker is %s',
                $event->getDead()->getName(),
                $event->getAttacker()->getName()
            );
        }

        return '';
    }
}
