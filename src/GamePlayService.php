<?php
declare(strict_types=1);

namespace Emagia;

use Emagia\MediatorPattern\Colleague;
use Emagia\MediatorPattern\ColleagueInterface;
use Emagia\Unit\UnitFactory;
use Emagia\Unit\UnitInterface;

/**
 * Imitates application service
 */
class GamePlayService implements ColleagueInterface
{
    use Colleague;

    /** @var int */
    private const MAX_ROUNDS = 20;
    /** @var TurnService */
    private $turn;
    /** @var UnitFactory */
    private $unitFactory;
    /** @var AttackerResolver */
    private $attackResolver;
    /** @var MediatorFactory */
    private $mediatorFactory;

    /**
     * @param TurnService      $turn
     * @param UnitFactory      $unitFactory
     * @param AttackerResolver $attackResolver
     * @param MediatorFactory  $mediatorFactory
     */
    public function __construct(
        TurnService $turn,
        UnitFactory $unitFactory,
        AttackerResolver $attackResolver,
        MediatorFactory $mediatorFactory
    ) {
        $this->turn = $turn;
        $this->unitFactory = $unitFactory;
        $this->attackResolver = $attackResolver;
        $this->mediatorFactory = $mediatorFactory;
    }

    public function startBattle(): void
    {
        $turnNo = 1;
        $wildBeast = $this->unitFactory->createWildBeast();
        $orderus = $this->unitFactory->createOrderus();
        $this->mediatorFactory->createMediatorForColleagues(
            $wildBeast,
            $orderus,
            $this->attackResolver,
            $this->turn,
            $this
        );

        $firstAttacker = $this->attackResolver->resolveAttacker($wildBeast, $orderus);
        $firstDefender = $this->getDefender($wildBeast, $orderus, $firstAttacker);

        $this->getMediator()->throwGameStartedEvent(
            $firstAttacker->getName(),
            $firstAttacker->getCurrentHealth(),
            $firstAttacker->getAttackStrength(),
            $firstAttacker->getDefense(),
            $firstAttacker->getSpeed(),
            $firstAttacker->getLuck(),
            $firstDefender->getName(),
            $firstDefender->getCurrentHealth(),
            $firstDefender->getAttackStrength(),
            $firstDefender->getDefense(),
            $firstDefender->getSpeed(),
            $firstDefender->getLuck(),
            self::MAX_ROUNDS
        );

        while ($this->keepFighting($firstAttacker, $firstDefender, $turnNo)) {
            $this->makeTurn($firstAttacker, $firstDefender, $turnNo);
            $turnNo++;
        }
    }

    private function keepFighting(UnitInterface $firstUnit, UnitInterface $secondUnit, int $turn): bool
    {
        $keepFighting = $firstUnit->isAlive() && $secondUnit->isAlive() && $turn <= 20;

        return $keepFighting ?: $this->notifyMediatorAboutFinishedFight($firstUnit, $secondUnit, $turn);
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

    private function notifyMediatorAboutFinishedFight(
        UnitInterface $firstUnit,
        UnitInterface $secondUnit,
        int $turn
    ): bool
    {
        $lastTurn = $turn - 1;
        if ($turn > self::MAX_ROUNDS) {
            $this->getMediator()->throwGameFinishedWithoutWinnerEvent(
                $firstUnit->getName(),
                $firstUnit->getCurrentHealth(),
                $secondUnit->getName(),
                $secondUnit->getCurrentHealth(),
                $lastTurn
            );

            return false;
        }

        if (!$firstUnit->isAlive()) {
            $this->getMediator()->throwGameFinishedWithWinner(
                $secondUnit->getName(),
                $secondUnit->getCurrentHealth(),
                $lastTurn
            );
        }

        if (!$secondUnit->isAlive()) {
            $this->getMediator()->throwGameFinishedWithWinner(
                $firstUnit->getName(),
                $firstUnit->getCurrentHealth(),
                $lastTurn
            );
        }

        return false;
    }
}
