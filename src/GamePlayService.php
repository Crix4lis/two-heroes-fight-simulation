<?php
declare(strict_types=1);

namespace Emagia;

use Emagia\Unit\UnitFactory;
use Emagia\Unit\UnitInterface;

/**
 * Imitates application service
 */
class GamePlayService
{
    /** @var TurnService */
    private $turn;
    /** @var UnitFactory */
    private $unitFactory;

    public function startBattle(): void
    {
        $turnNo = 1;
        $wildBeast = $this->unitFactory->createWildBeast();
        $orderus = $this->unitFactory->createOrderus();

        while ($this->keepFighting($firstAttacker, $firstDefender, $turnNo)) {
            $this->makeTurn($firstAttacker, $firstDefender, $turnNo);
        }
    }

    private function keepFighting(UnitInterface $firstUnit, UnitInterface $secondUnit, int $turn): bool
    {
        return $firstUnit->isAlive() && $secondUnit->isAlive() && $turn <= 20;
    }

    private function makeTurn(UnitInterface $firstAttacker, UnitInterface $firstDefender, int $turn): void
    {
        if ($turn % 2 === 0) {
            $this->turn->make($firstAttacker, $firstDefender);
            return;
        }

        $this->turn->make($firstDefender, $firstAttacker);
    }
}
