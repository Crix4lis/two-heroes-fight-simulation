<?php
declare(strict_types=1);

namespace Emagia;

use Emagia\Event\GameFinishedWithoutWinnerEvent;
use Emagia\Event\GameFinishedWithWinner;
use Emagia\Event\GameStartedEvent;
use Emagia\Event\TurnStartsEvent;
use Emagia\ObserverPattern\ObserverInterface;
use Emagia\ObserverPattern\Subject;
use Emagia\ObserverPattern\SubjectInterface;
use Emagia\Unit\UnitFactory;
use Emagia\Unit\UnitInterface;

/**
 * Imitates application service
 */
class GamePlayService implements SubjectInterface
{
    use Subject {
        register as protected registerObserver;
    }

    /** @var TurnServiceInterface */
    private $turn;
    /** @var UnitFactory */
    private $unitFactory;
    /** @var AttackerResolverInterface */
    private $attackResolver;
    /** @var int */
    private const MAX_ROUNDS = 20;
    /** @var ObserverInterface[] */
    private $unitEventObservers = [];

    /**
     * @param TurnServiceInterface      $turn
     * @param UnitFactory               $unitFactory
     * @param AttackerResolverInterface $attackResolver
     */
    public function __construct(
        TurnServiceInterface $turn,
        UnitFactory $unitFactory,
        AttackerResolverInterface $attackResolver
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
        $this->registerObserversForUnits($firstAttacker, $firstDefender);

        $this->notifyObservers(new GameStartedEvent(
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
        ));

        while ($this->willKeepFighting($firstAttacker, $firstDefender, $turnNo)) {
            $this->makeTurn($firstAttacker, $firstDefender, $turnNo);
            $turnNo++;
        }
    }

    public function register(ObserverInterface $observer): void
    {
        $this->unitEventObservers[] = $observer;
        $this->registerObserver($observer);
    }

    private function willKeepFighting(UnitInterface $firstUnit, UnitInterface $secondUnit, int $turn): bool
    {
        $keepFighting = $firstUnit->isAlive() && $secondUnit->isAlive() && $turn <= self::MAX_ROUNDS;

        return $keepFighting ?: $this->notifyObserversAboutFinishedFight($firstUnit, $secondUnit, $turn);
    }

    private function notifyObserversAboutFinishedFight(
        UnitInterface $firstUnit,
        UnitInterface $secondUnit,
        int $turn
    ): bool
    {
        $lastTurn = $turn - 1;

        if ($turn > self::MAX_ROUNDS) {
            $this->notifyObservers(new GameFinishedWithoutWinnerEvent(
                $firstUnit->getName(),
                $firstUnit->getCurrentHealth(),
                $secondUnit->getName(),
                $secondUnit->getCurrentHealth(),
                $lastTurn
            ));
            return false;
        }

        if (!$firstUnit->isAlive()) {
            $this->notifyObservers(new GameFinishedWithWinner(
                $secondUnit->getName(),
                $secondUnit->getCurrentHealth(),
                $lastTurn
            ));
        }

        if (!$secondUnit->isAlive()) {
            $this->notifyObservers(new GameFinishedWithWinner(
                $firstUnit->getName(),
                $firstUnit->getCurrentHealth(),
                $lastTurn
            ));
        }

        return false;
    }

    private function makeTurn(UnitInterface $firstAttacker, UnitInterface $firstDefender, int $turn): void
    {
        if ($turn % 2 !== 0) { //because iteration starts with 1 not 0, first attack should be made when 1 % 2 = 1
            $this->notifyObservers(new TurnStartsEvent($firstAttacker->getName(), $firstDefender->getName(), $turn));
            $this->turn->make($firstAttacker, $firstDefender);
            return;
        }

        $this->notifyObservers(new TurnStartsEvent($firstDefender->getName(), $firstAttacker->getName(), $turn));
        $this->turn->make($firstDefender, $firstAttacker);
    }

    private function getDefender(UnitInterface $u1, UnitInterface $u2, UnitInterface $attacker): UnitInterface
    {
        return $u1->isTheSameInstance($attacker) ? $u2 : $u1;
    }

    /**
     * @param UnitInterface|Subject $firstAttacker
     * @param UnitInterface|Subject $firstDefender
     */
    private function registerObserversForUnits(UnitInterface $firstAttacker, UnitInterface $firstDefender): void
    {
        foreach ($this->unitEventObservers as $ob) {
            $firstDefender->register($ob);
            $firstAttacker->register($ob);
        }
    }
}
