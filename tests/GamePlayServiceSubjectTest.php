<?php
declare(strict_types=1);

namespace Test\Emagia;

use Emagia\AttackerResolver;
use Emagia\AttackerResolverInterface;
use Emagia\Event\GameFinishedWithoutWinnerEvent;
use Emagia\Event\GameFinishedWithWinner;
use Emagia\Event\GameStartedEvent;
use Emagia\Event\TurnStartsEvent;
use Emagia\GamePlayService;
use Emagia\ObserverPattern\ObserverInterface;
use Emagia\ObserverPattern\SubjectInterface;
use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Luck;
use Emagia\Property\Speed;
use Emagia\Property\Strength;
use Emagia\TurnServiceInterface;
use Emagia\Unit\UnitFactory;
use Emagia\Unit\UnitInterface;
use PHPUnit\Framework\TestCase;

class GamePlayServiceSubjectTest extends TestCase
{
    /**
     * @var \Emagia\Unit\UnitFactory|\Prophecy\Prophecy\ObjectProphecy
     */
    private $factory;
    /**
     * @var AttackerResolverInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $attackResolver;
    /**
     * @var \Emagia\TurnServiceInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $turn;
    /**
     * @var UnitSubjectInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $orderus;
    /**
     * @var UnitSubjectInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $wildBeast;
    /**
     * @var \Emagia\ObserverPattern\ObserverInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $observer;
    /**
     * @var \Emagia\Property\HealthPoints|\Prophecy\Prophecy\ObjectProphecy
     */
    private $hp;
    /**
     * @var \Emagia\Property\Strength|\Prophecy\Prophecy\ObjectProphecy
     */
    private $str;
    /**
     * @var \Emagia\Property\Defence|\Prophecy\Prophecy\ObjectProphecy
     */
    private $def;
    /**
     * @var \Emagia\Property\Luck|\Prophecy\Prophecy\ObjectProphecy
     */
    private $luck;
    /**
     * @var \Emagia\Property\Speed|\Prophecy\Prophecy\ObjectProphecy
     */
    private $speed;

    public function setUp(): void
    {
        $this->factory = $this->prophesize(UnitFactory::class);
        $this->attackResolver = $this->prophesize(AttackerResolver::class);
        $this->turn = $this->prophesize(TurnServiceInterface::class);
        $this->wildBeast = $this->prophesize(UnitSubjectInterface::class);
        $this->orderus = $this->prophesize(UnitSubjectInterface::class);
        $this->observer = $this->prophesize(ObserverInterface::class);
        $this->hp = $this->prophesize(HealthPoints::class);
        $this->str = $this->prophesize(Strength::class);
        $this->def = $this->prophesize(Defence::class);
        $this->speed = $this->prophesize(Speed::class);
        $this->luck = $this->prophesize(Luck::class);
    }

    public function oddTurnsDataProvider(): array
    {
        return [
            '1 round' => [
                [true, false, false], // turn def: round, before next, when dead
                [true, true], // turn att: round, before next round
                [1],// odd turn
                []// even turn
            ],
            '3 rounds' => [
                [true, true, true, false, false],
                [true, true, true, true],
                [1, 3],
                [2]
            ],
            '5 rounds' => [
                [true, true, true, true, true, false, false],
                [true, true, true, true, true, true],
                [1, 3, 5],
                [2, 4]
            ],
            '19 rounds' => [
                [true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, false, false],
                [true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true],
                [1, 3, 5, 7, 9, 11, 13, 15, 17, 19],
                [2, 4, 6, 8, 10, 12, 14, 16, 18]
            ]
        ];
    }

    /**
     * @dataProvider oddTurnsDataProvider
     *
     * @param bool[] $firstAttackerAlive
     * @param bool[] $firstDefenderAlive
     * @param int[]  $oddTurn
     * @param int[]  $evenTurn
     */
    public function testPlaysBattleOdddNumberOfTurnsWithOrderusAsFirstAttackerForNRounds(
        array $firstAttackerAlive,
        array $firstDefenderAlive,
        array $oddTurn,
        array $evenTurn
    ): void
    {
        $firstAttackerName = 'orderus-first-attacker';
        $firstDefendrName = 'beast-first-defender';

        //attacker
        $this->orderus->getName()->willReturn($firstAttackerName);
        $this->orderus->getCurrentHealth()->willReturn($this->hp->reveal());
        $this->orderus->getAttackStrength()->willReturn($this->str->reveal());
        $this->orderus->getDefense()->willReturn($this->def->reveal());
        $this->orderus->getSpeed()->willReturn($this->speed->reveal());
        $this->orderus->getLuck()->willReturn($this->luck->reveal());
        $this->orderus->register($this->observer->reveal());

        //defender
        $this->wildBeast->getName()->willReturn($firstDefendrName);
        $this->wildBeast->getCurrentHealth()->willReturn($this->hp->reveal());
        $this->wildBeast->getAttackStrength()->willReturn($this->str->reveal());
        $this->wildBeast->getDefense()->willReturn($this->def->reveal());
        $this->wildBeast->getSpeed()->willReturn($this->speed->reveal());
        $this->wildBeast->getLuck()->willReturn($this->luck->reveal());
        $this->wildBeast->register($this->observer->reveal());

        $this->observer->update(new GameStartedEvent(
            $firstAttackerName,
            $this->hp->reveal(),
            $this->str->reveal(),
            $this->def->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal(),
            $firstDefendrName,
            $this->hp->reveal(),
            $this->str->reveal(),
            $this->def->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal(),
            20
        ))->shouldBeCalledTimes(1);

        foreach ($oddTurn as $oddT) {
            $this->observer->update(new TurnStartsEvent($firstAttackerName, $firstDefendrName, $oddT))
                ->shouldBeCalledTimes(1);
        }
        foreach ($evenTurn as $evenT) {
            $this->observer->update(new TurnStartsEvent($firstDefendrName, $firstAttackerName, $evenT))
                ->shouldBeCalledTimes(1);
        }
        $this->observer->update(new GameFinishedWithWinner(
            $firstDefendrName,
            $this->hp->reveal(),
            end($oddTurn)
        ));

        $this->wildBeast->isAlive()->willReturn(...$firstDefenderAlive);
        $this->orderus->isAlive()->willReturn(...$firstAttackerAlive);
        $this->wildBeast->isTheSameInstance($this->orderus->reveal())->willReturn(false);
        $this->factory->createWildBeast()->willReturn($this->wildBeast->reveal());
        $this->factory->createOrderus()->willReturn($this->orderus->reveal());
        $this->attackResolver->resolveAttacker($this->wildBeast->reveal(), $this->orderus->reveal())
            ->willReturn($this->orderus->reveal());
        $this->turn->make($this->orderus->reveal(), $this->wildBeast->reveal());
        $this->turn->make($this->wildBeast->reveal(), $this->orderus->reveal());

        $gameplay = new GamePlayService(
            $this->turn->reveal(),
            $this->factory->reveal(),
            $this->attackResolver->reveal()
        );
        $gameplay->register($this->observer->reveal());

        $gameplay->startBattle();
    }


    public function evenTurnsDataProvider(): array
    {
        return [
            '1 round' => [
                [true, true, true], // turn def: round, before next, when dead
                [true, true, false], // turn att: round, before next round
                [1],// odd turn
                [2]// even turn
            ],
            '4 rounds' => [
                [true, true, true, true, true],
                [true, true, true, true, false],
                [1, 3],
                [2, 4]
            ],
            '18 rounds' => [
                [true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true],
                [true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, false],
                [1, 3, 5, 7, 9, 11, 13, 15, 17],
                [2, 4, 6, 8, 10, 12, 14, 16, 18]
            ],
        ];
    }

    /**
     * @dataProvider evenTurnsDataProvider
     *
     * @param bool[] $firstAttackerAlive
     * @param bool[] $firstDefenderAlive
     * @param int[]  $oddTurn
     * @param int[]  $evenTurn
     */
    public function testPlaysBattleEvenNumberOfTurnsWithOrderusAsFirstAttackerForNRounds(
        array $firstAttackerAlive,
        array $firstDefenderAlive,
        array $oddTurn,
        array $evenTurn
    ): void
    {
        $firstAttackerName = 'orderus-first-attacker';
        $firstDefendrName = 'beast-first-defender';

        //attacker
        $this->orderus->getName()->willReturn($firstAttackerName);
        $this->orderus->getCurrentHealth()->willReturn($this->hp->reveal());
        $this->orderus->getAttackStrength()->willReturn($this->str->reveal());
        $this->orderus->getDefense()->willReturn($this->def->reveal());
        $this->orderus->getSpeed()->willReturn($this->speed->reveal());
        $this->orderus->getLuck()->willReturn($this->luck->reveal());
        $this->orderus->register($this->observer->reveal());

        //defender
        $this->wildBeast->getName()->willReturn($firstDefendrName);
        $this->wildBeast->getCurrentHealth()->willReturn($this->hp->reveal());
        $this->wildBeast->getAttackStrength()->willReturn($this->str->reveal());
        $this->wildBeast->getDefense()->willReturn($this->def->reveal());
        $this->wildBeast->getSpeed()->willReturn($this->speed->reveal());
        $this->wildBeast->getLuck()->willReturn($this->luck->reveal());
        $this->wildBeast->register($this->observer->reveal());

        $this->observer->update(new GameStartedEvent(
            $firstAttackerName,
            $this->hp->reveal(),
            $this->str->reveal(),
            $this->def->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal(),
            $firstDefendrName,
            $this->hp->reveal(),
            $this->str->reveal(),
            $this->def->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal(),
            20
        ))->shouldBeCalledTimes(1);

        foreach ($oddTurn as $oddT) {
            $this->observer->update(new TurnStartsEvent($firstAttackerName, $firstDefendrName, $oddT))
                ->shouldBeCalledTimes(1);
        }
        foreach ($evenTurn as $evenT) {
            $this->observer->update(new TurnStartsEvent($firstDefendrName, $firstAttackerName, $evenT))
                ->shouldBeCalledTimes(1);
        }
        $this->observer->update(new GameFinishedWithWinner(
            $firstAttackerName,
            $this->hp->reveal(),
            end($evenTurn)
        ));

        $this->wildBeast->isAlive()->willReturn(...$firstDefenderAlive);
        $this->orderus->isAlive()->willReturn(...$firstAttackerAlive);
        $this->wildBeast->isTheSameInstance($this->orderus->reveal())->willReturn(false);
        $this->factory->createWildBeast()->willReturn($this->wildBeast->reveal());
        $this->factory->createOrderus()->willReturn($this->orderus->reveal());
        $this->attackResolver->resolveAttacker($this->wildBeast->reveal(), $this->orderus->reveal())
            ->willReturn($this->orderus->reveal());
        $this->turn->make($this->orderus->reveal(), $this->wildBeast->reveal());
        $this->turn->make($this->wildBeast->reveal(), $this->orderus->reveal());

        $gameplay = new GamePlayService(
            $this->turn->reveal(),
            $this->factory->reveal(),
            $this->attackResolver->reveal()
        );
        $gameplay->register($this->observer->reveal());

        $gameplay->startBattle();
    }

    public function testPlaysBattleEvenAndMaximumNumberOfTurnsWithOrderusAsFirstAttackerForNRounds(): void
    {
        $firstAttackerName = 'orderus-first-attacker';
        $firstDefendrName = 'beast-first-defender';

        $firstAttackerAlive = [true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true];
        $firstDefenderAlive = [true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true, true];
        $oddTurn = [1, 3, 5, 7, 9, 11, 13, 15, 17, 19];
        $evenTurn = [2, 4, 6, 8, 10, 12, 14, 16, 18, 20];

        //attacker
        $this->orderus->getName()->willReturn($firstAttackerName);
        $this->orderus->getCurrentHealth()->willReturn($this->hp->reveal());
        $this->orderus->getAttackStrength()->willReturn($this->str->reveal());
        $this->orderus->getDefense()->willReturn($this->def->reveal());
        $this->orderus->getSpeed()->willReturn($this->speed->reveal());
        $this->orderus->getLuck()->willReturn($this->luck->reveal());
        $this->orderus->register($this->observer->reveal());

        //defender
        $this->wildBeast->getName()->willReturn($firstDefendrName);
        $this->wildBeast->getCurrentHealth()->willReturn($this->hp->reveal());
        $this->wildBeast->getAttackStrength()->willReturn($this->str->reveal());
        $this->wildBeast->getDefense()->willReturn($this->def->reveal());
        $this->wildBeast->getSpeed()->willReturn($this->speed->reveal());
        $this->wildBeast->getLuck()->willReturn($this->luck->reveal());
        $this->wildBeast->register($this->observer->reveal());

        $this->observer->update(new GameStartedEvent(
            $firstAttackerName,
            $this->hp->reveal(),
            $this->str->reveal(),
            $this->def->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal(),
            $firstDefendrName,
            $this->hp->reveal(),
            $this->str->reveal(),
            $this->def->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal(),
            20
        ))->shouldBeCalledTimes(1);

        foreach ($oddTurn as $oddT) {
            $this->observer->update(new TurnStartsEvent($firstAttackerName, $firstDefendrName, $oddT))
                ->shouldBeCalledTimes(1);
        }
        foreach ($evenTurn as $evenT) {
            $this->observer->update(new TurnStartsEvent($firstDefendrName, $firstAttackerName, $evenT))
                ->shouldBeCalledTimes(1);
        }
        $this->observer->update(new GameFinishedWithoutWinnerEvent(
            $firstAttackerName,
            $this->hp->reveal(),
            $firstDefendrName,
            $this->hp->reveal(),
            20
        ));

        $this->wildBeast->isAlive()->willReturn(...$firstDefenderAlive);
        $this->orderus->isAlive()->willReturn(...$firstAttackerAlive);
        $this->wildBeast->isTheSameInstance($this->orderus->reveal())->willReturn(false);
        $this->factory->createWildBeast()->willReturn($this->wildBeast->reveal());
        $this->factory->createOrderus()->willReturn($this->orderus->reveal());
        $this->attackResolver->resolveAttacker($this->wildBeast->reveal(), $this->orderus->reveal())
            ->willReturn($this->orderus->reveal());
        $this->turn->make($this->orderus->reveal(), $this->wildBeast->reveal());
        $this->turn->make($this->wildBeast->reveal(), $this->orderus->reveal());

        $gameplay = new GamePlayService(
            $this->turn->reveal(),
            $this->factory->reveal(),
            $this->attackResolver->reveal()
        );
        $gameplay->register($this->observer->reveal());

        $gameplay->startBattle();
    }
}

interface UnitSubjectInterface extends UnitInterface, SubjectInterface {}
