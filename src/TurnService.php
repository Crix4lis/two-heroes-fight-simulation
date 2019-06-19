<?php
declare(strict_types=1);

namespace Emagia;

use Emagia\MediatorPattern\Colleague;
use Emagia\MediatorPattern\ColleagueInterface;
use Emagia\Unit\UnitInterface;
use LogicException;

/**
 * Imitates domain service
 */
class TurnService implements ColleagueInterface
{
    use Colleague;

    public function make(UnitInterface $attacker, UnitInterface $defender): void
    {
        if (!$defender->isAlive()) {
            return;
        }

        if (!$attacker->isAlive()) {
            $this->getMediator()->logCriticalAttackerIsDead($attacker, $defender);
            throw new LogicException('This should not happen!');
        }

        $attacker->performAttack($defender);
    }
}
