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
use Emagia\ObserverPattern\Event;
use Emagia\ObserverPattern\ObserverInterface;
use Monolog\Logger as MonoLogger;

class EventLogger extends BaseLogger implements ObserverInterface
{
    /** @var ObserverInterface */
    private $observer;

    public function __construct(
        ObserverInterface $decoratedObserver,
        MonoLogger $logger,
        int $level = MonoLogger::ERROR,
        string $logFilePath = BaseLogger::DEFAULT_FILE
    )
    {
        parent::__construct($logger, $level, $logFilePath);
        $this->observer = $decoratedObserver;
    }

    public function update(Event $event): void
    {
        if ($this->level <= MonoLogger::DEBUG) {
            $this->logOnDebugMode($event);
        }

        if ($this->level <= MonoLogger::ERROR) {
            $this->logOnErrorMode($event);
        }

        $this->observer->update($event);
    }

    private function logOnDebugMode(Event $event): void
    {
        if ($event instanceof BlockedDamageEvent) {
            $str = sprintf(
                '%s blocked %s damage',
                ucfirst($event->getDefenderName()),
                $event->getDamage()->getPoints()
            );
        }

        if ($event instanceof GameFinishedWithoutWinnerEvent) {
            $str = sprintf(
                'Game over! Noone has won! First attacker %s has %s hp left. First defender %s has %s hp left.',
                ucfirst($event->getFirstUnitName()),
                $event->getFirstUnitHp()->getPoints(),
                ucfirst($event->getSecondUnitName()),
                $event->getSecondUnitHp()->getPoints()
            );
        }

        if ($event instanceof GameFinishedWithWinner) {
            $str = sprintf(
                'Game over! Winner is: %s! %s has %s hp left',
                strtoupper($event->getWinnerName()),
                ucfirst($event->getWinnerName()),
                $event->getWinnerHp()
            );
        }

        if ($event instanceof GameStartedEvent) {
            $info = 'GAME STARTED!\n First attacker is: %s'
                .'with stats:\n'
                .'health points: %s\n'
                .'strength: %s\n'
                .'defence: %s\n'
                .'speed: %s\n'
                .'luck: %s\n'
                .'First defender is: %s'
                .'with stats:\n'
                .'health points: %s\n'
                .'strength: %s\n'
                .'defence: %s\n'
                .'speed: %s\n'
                .'luck: %s\n'
                .'max rounds are set to: %s';

            $str = sprintf(
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

        if ($event instanceof MagicShieldUsedEvent) {
            $str = sprintf(
                '%s uses magic shield! Damage to receive reduced to %s',
                strtoupper($event->getDefenderName()),
                $event->getDamageReducedTo()
            );
        }

        if ($event instanceof PerformedAttackEvent) {
            $str = sprintf(
                '%s attacked with %s dmg.',
                ucfirst($event->getAttackerName()),
                $event->getAttackedWithDamage()
            );
        }

        if ($event instanceof RapidStrikeUsedEvent) {
            $str = sprintf(
                '%s uses rapid strike. Attacks again!',
                ucfirst($event->getAttackerName())
            );
        }

        if ($event instanceof ReceivedDamageEvent) {
            $str = sprintf(
                '%s receives %s damage. %s hp left.',
                ucfirst($event->getDefenderName()),
                $event->getReceivedDamage(),
                $event->getDefenderHpLeft()
            );
        }

        if ($event instanceof TurnStartsEvent) {
            $str = sprintf(
                'Turn no. %s. %s attacks %s',
                $event->getTurn(),
                ucfirst($event->getAttackerName()),
                ucfirst($event->getDefenderName())
            );
        }

        if ($event instanceof UnitDiedEvent) {
            $str = sprintf(
                '%s JUST DIED!',
                $event->getUnitName()
            );
        }

        $this->logger->debug($str);
    }

    private function logOnErrorMode(Event $event): void
    {
        if ($event instanceof DefenderAlredyDeadEvent) {
            $str = sprintf(
                'Defender named: %s is already dead! Attacker is %s',
                $event->getDead(),
                $event->getAttacker()
            );
            $this->logger->error($str);
        }
    }
}
