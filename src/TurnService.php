<?php
declare(strict_types=1);

namespace Emagia;

use Emagia\Unit\UnitInterface;
use LogicException;

/**
 * Imitates domain service
 */
class TurnService
{
    public function makeTurn(UnitInterface $attacker, UnitInterface $defender): void
    {
        if (!$defender->isAlive()) {
            return;
        }

        if (!$attacker->isAlive()) {
            throw new LogicException('This should not happen!');
        }

        $attacker->performAttack($defender);
    }
}
