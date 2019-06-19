<?php
declare(strict_types=1);

namespace Emagia\Reader;

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
use Emagia\MediatorPattern\Colleague;
use Emagia\MediatorPattern\Event;

class GameReader extends Colleague implements GameReaderInterface
{
    public function printEvent(Event $event): void
    {
        if ($event instanceof BlockedDamageEvent) {
            printf("%s blocked %s damage\n", ucfirst($event->getDefenderName()), $event->getDamage()->getPoints());
        }

        if ($event instanceof DefenderAlredyDeadEvent) {
            echo 'DEFENDER DEAD!!!';
        }

        if ($event instanceof GameFinishedWithoutWinnerEvent) {
            printf(
                    "\n\n***** GAME OVER! *****\nNoone has won! %s has %s hp left. %s has %s hp left.\n",
                ucfirst($event->getFirstUnitName()),
                $event->getFirstUnitHp()->getPoints(),
                ucfirst($event->getSecondUnitName()),
                $event->getSecondUnitHp()->getPoints()
            );
        }

        if ($event instanceof GameFinishedWithWinner) {
            printf(
                "\n\n***** GAME OVER! *****\nWinner is: %s! %s has %s hp left\n",
                strtoupper($event->getWinnerName()),
                ucfirst($event->getWinnerName()),
                $event->getWinnerHp()
            );
        }

        if ($event instanceof GameStartedEvent) {
            $info = "\n***** GAME STARTED! *****\nFirst attacks: %s"
                . "\nwith stats:\n"
                . "  Health Points: %s\n"
                . "  Strength: %s\n"
                . "  Defence: %s\n"
                . "  Speed: %s\n"
                . "  Luck: %s\n\n"
                . "First defends: %s\n"
                . "with stats:\n"
                . "  Health Points: %s\n"
                . "  Strength: %s\n"
                . "  Defence: %s\n"
                . "  Speed: %s\n"
                . "  Luck: %s\n\n"
                . "Max rounds are set to: %s\n\n\n";

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

            echo $str;
        }

        if ($event instanceof MagicShieldUsedEvent) {
            printf(
                "%s uses magic shield! Damage to receive reduced to %s\n",
                strtoupper($event->getDefenderName()),
                $event->getDamageReducedTo()
            );
        }

        if ($event instanceof PerformedAttackEvent) {
            printf(
                "%s attacked with %s dmg.\n",
                ucfirst($event->getAttackerName()),
                $event->getAttackedWithDamage()
            );
        }

        if ($event instanceof RapidStrikeUsedEvent) {
            printf(
                "%s uses rapid strike. Attacks again!\n",
                ucfirst($event->getAttackerName())
            );
        }

        if ($event instanceof ReceivedDamageEvent) {
            printf(
                "%s receives %s damage. %s hp left.\n",
                ucfirst($event->getDefenderName()),
                $event->getReceivedDamage(),
                $event->getDefenderHpLeft()
            );
        }

        if ($event instanceof TurnStartsEvent) {
            printf(
                "\n\nTURN NO. %s. %s attacks %s\n\n",
                $event->getTurn(),
                ucfirst($event->getAttackerName()),
                ucfirst($event->getDefenderName())
            );
        }

        if ($event instanceof UnitDiedEvent) {
            printf(
                "%s JUST DIED!\n",
                $event->getUnitName()
            );
        }
    }
}
