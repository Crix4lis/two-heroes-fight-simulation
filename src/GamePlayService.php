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
    /** @var AttackerResolver */
    private $attackResolver;

    /**
     * @param TurnService $turn
     * @param UnitFactory $unitFactory
     * @param AttackerResolver $attackResolver
     */
    public function __construct(
        TurnService $turn,
        UnitFactory $unitFactory,
        AttackerResolver $attackResolver
    ) {
        $this->turn = $turn;
        $this->unitFactory = $unitFactory;
        $this->attackResolver = $attackResolver;
    }

    public function startBattle(): void
    {
        $turnNo = 1;
        $wildBeast = $this->unitFactory->createWildBeast();
        $orderus = $this->unitFactory->createOrderus();

        $firstAttacker = $this->attackResolver->resolveAttacker($wildBeast, $orderus);
        $firstDefender = $this->getDefender($wildBeast, $orderus, $firstAttacker);

        while ($this->keepFighting($firstAttacker, $firstDefender, $turnNo)) {
            $this->makeTurn($firstAttacker, $firstDefender, $turnNo);
            $turnNo++;
        }
    }

    private function keepFighting(UnitInterface $firstUnit, UnitInterface $secondUnit, int $turn): bool
    {
        return $firstUnit->isAlive() && $secondUnit->isAlive() && $turn <= 20;
    }

    private function makeTurn(UnitInterface $firstAttacker, UnitInterface $firstDefender, int $turn): void
    {
        if ($turn % 2 !== 0) { //because iteration starts with 1 not 0, first attack should be made when 1 % 2 = 1
            $this->turn->make($firstAttacker, $firstDefender);
            return;
        }

        $this->turn->make($firstDefender, $firstAttacker);
    }

    private function getDefender(UnitInterface $u1, UnitInterface $u2, UnitInterface $attacker): UnitInterface
    {
        return $u1->isTheSameInstance($attacker) ? $u2 : $u1;
    }
}
