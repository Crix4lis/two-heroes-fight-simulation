<?php
declare(strict_types=1);

namespace Emagia;

use Emagia\MediatorPattern\Colleague;
use Emagia\Unit\UnitInterface;
use LogicException;

/**
 * Imitates domain service
 */
class TurnService extends Colleague
{
    public function make(UnitInterface $attacker, UnitInterface $defender): void
    {
        if (!$defender->isAlive()) {
            return;
        }

        if (!$attacker->isAlive()) {
            $this->mediator->logCriticalAttackerIsDead($attacker, $defender);
            throw new LogicException('This should not happen!');
        }

        $attacker->performAttack($defender);
    }
}
