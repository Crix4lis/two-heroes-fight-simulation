<?php
declare(strict_types=1);

namespace Test\Emagia;

use Emagia\AttackerResolver;
use Emagia\GamePlayService;
use Emagia\MediatorFactory;
use Emagia\MediatorPattern\ColleagueInterface;
use Emagia\MediatorPattern\EventAndLogsMediatorInterface;
use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Luck;
use Emagia\Property\Speed;
use Emagia\Property\Strength;
use Emagia\TurnService;
use Emagia\Unit\UnitFactory;
use Emagia\Unit\UnitInterface;
use PHPUnit\Framework\TestCase;

class GamePlayServiceTest extends TestCase
{
    /**
     * @var \Emagia\Unit\UnitFactory|\Prophecy\Prophecy\ObjectProphecy
     */
    private $factory;
    /**
     * @var \Emagia\AttackerResolver|\Prophecy\Prophecy\ObjectProphecy
     */
    private $attackResolver;
    /**
     * @var \Emagia\TurnService|\Prophecy\Prophecy\ObjectProphecy
     */
    private $turn;
    /**
     * @var \Emagia\Unit\UnitInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $orderus;
    /**
     * @var \Emagia\Unit\UnitInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $wildBeast;
    /**
     * @var \Emagia\MediatorFactory|\Prophecy\Prophecy\ObjectProphecy
     */
    private $mediatorFactory;
    /**
     * @var \Emagia\MediatorPattern\EventAndLogsMediatorInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $mediator;
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
     * @var \Emagia\Property\Speed|\Prophecy\Prophecy\ObjectProphecy
     */
    private $speed;
    /**
     * @var \Prophecy\Prophecy\ObjectProphecy
     */
    private $luck;

    public function setUp(): void
    {
        $this->factory = $this->prophesize(UnitFactory::class);
        $this->attackResolver = $this->prophesize(AttackerResolver::class);
        $this->turn = $this->prophesize(TurnService::class);
        $this->wildBeast = $this->prophesize(UnitColleague::class);
        $this->orderus = $this->prophesize(UnitColleague::class);
        $this->mediatorFactory = $this->prophesize(MediatorFactory::class);
        $this->mediator = $this->prophesize(EventAndLogsMediatorInterface::class);
        $this->hp = $this->prophesize(HealthPoints::class);
        $this->str = $this->prophesize(Strength::class);
        $this->def = $this->prophesize(Defence::class);
        $this->speed = $this->prophesize(Speed::class);
        $this->luck = $this->prophesize(Luck::class);
    }

    public function turnsDataProvider(): array
    {
        return [
            '20 maximum rounds' => [
                [true, true, true, true, true, true, true, true, true, true], //first attacker
                [true, true, true, true, true, true, true, true, true, true], //first defender
                10,
                10
            ],
            '1 rounds' => [
                [true, true, true],
                [true, false, false],
                1,
                0
            ],
            '2 rounds' => [
                [true, true, true, true],
                [true, true, false, false],
                1,
                1
            ],
            '9 rounds' => [
                [true, true, true, true, true, true, true, true, true, true],
                [true, true, true, true, true, true, true, true, true, false],
                5,
                4
            ],
            '10 rounds' => [
                [true, true, true, true, true, true, true, true, true, true, true],
                [true, true, true, true, true, true, true, true, true, true, false],
                5,
                5
            ],
        ];
    }

    /**
     * @dataProvider turnsDataProvider
     *
     * @param bool[] $orderuAlive
     * @param bool[] $wildBeastAlive
     * @param int    $turnOrderusFirstTimes
     * @param int    $turnBeastFirstTimes
     */
    public function testPlaysBattleWithOrderusAsFirstAttackerForNRounds(
        array $orderuAlive,
        array $wildBeastAlive,
        int $turnOrderusFirstTimes,
        int $turnBeastFirstTimes
    ): void
    {
        $this->wildBeast->getName()->willReturn('beast');
        $this->wildBeast->getCurrentHealth()->willReturn($this->hp->reveal());
        $this->wildBeast->getAttackStrength()->willReturn($this->str->reveal());
        $this->wildBeast->getDefense()->willReturn($this->def->reveal());
        $this->wildBeast->getSpeed()->willReturn($this->speed->reveal());
        $this->wildBeast->getLuck()->willReturn($this->luck->reveal());

        $this->wildBeast->isAlive()->willReturn(...$wildBeastAlive);
        $this->orderus->isAlive()->willReturn(...$orderuAlive);

        $this->orderus->getName()->willReturn('orderus');
        $this->orderus->getCurrentHealth()->willReturn($this->hp->reveal());
        $this->orderus->getAttackStrength()->willReturn($this->str->reveal());
        $this->orderus->getDefense()->willReturn($this->def->reveal());
        $this->orderus->getSpeed()->willReturn($this->speed->reveal());
        $this->orderus->getLuck()->willReturn($this->luck->reveal());

        $this->wildBeast->isTheSameInstance($this->orderus->reveal())->willReturn(false)->shouldBeCalled();
        $this->factory->createWildBeast()->willReturn($this->wildBeast->reveal());
        $this->factory->createOrderus()->willReturn($this->orderus->reveal());
        $this->attackResolver->resolveAttacker($this->wildBeast->reveal(), $this->orderus->reveal())
            ->willReturn($this->orderus->reveal());
        $this->turn->make($this->orderus->reveal(), $this->wildBeast->reveal())
            ->shouldBeCalledTimes($turnOrderusFirstTimes);
        $this->turn->make($this->wildBeast->reveal(), $this->orderus->reveal())
            ->shouldBeCalledTimes($turnBeastFirstTimes);

        $gameplay = new GamePlayService(
            $this->turn->reveal(),
            $this->factory->reveal(),
            $this->attackResolver->reveal(),
            $this->mediatorFactory->reveal()
        );

        $gameplay->setMediator($this->mediator->reveal());
        $this->mediatorFactory->createMediatorForColleagues(
            $this->wildBeast->reveal(),
            $this->orderus->reveal(),
            $this->attackResolver->reveal(),
            $this->turn->reveal(),
            $gameplay
        )->willReturn($this->mediator->reveal());

        $gameplay->startBattle();
    }

    /**
     * @dataProvider turnsDataProvider
     *
     * @param bool[] $wildBeastAlive
     * @param bool[] $orderuAlive
     * @param int    $turnBeastFirstTimes
     * @param int    $turnOrderusFirstTimes
     */
    public function testPlaysBattleWithWildBeastAsFirstAttackerForNRounds(
        array $wildBeastAlive,
        array $orderuAlive,
        int $turnBeastFirstTimes,
        int $turnOrderusFirstTimes
    ): void
    {
        $this->wildBeast->getName()->willReturn('beast');
        $this->wildBeast->getCurrentHealth()->willReturn($this->hp->reveal());
        $this->wildBeast->getAttackStrength()->willReturn($this->str->reveal());
        $this->wildBeast->getDefense()->willReturn($this->def->reveal());
        $this->wildBeast->getSpeed()->willReturn($this->speed->reveal());
        $this->wildBeast->getLuck()->willReturn($this->luck->reveal());

        $this->wildBeast->isAlive()->willReturn(...$wildBeastAlive);
        $this->orderus->isAlive()->willReturn(...$orderuAlive);

        $this->orderus->getName()->willReturn('orderus');
        $this->orderus->getCurrentHealth()->willReturn($this->hp->reveal());
        $this->orderus->getAttackStrength()->willReturn($this->str->reveal());
        $this->orderus->getDefense()->willReturn($this->def->reveal());
        $this->orderus->getSpeed()->willReturn($this->speed->reveal());
        $this->orderus->getLuck()->willReturn($this->luck->reveal());

        $this->wildBeast->isTheSameInstance($this->wildBeast->reveal())->willReturn(true)->shouldBeCalled();
        $this->factory->createWildBeast()->willReturn($this->wildBeast->reveal());
        $this->factory->createOrderus()->willReturn($this->orderus->reveal());
        $this->attackResolver->resolveAttacker($this->wildBeast->reveal(), $this->orderus->reveal())
            ->willReturn($this->wildBeast->reveal());
        $this->turn->make($this->wildBeast->reveal(), $this->orderus->reveal())
            ->shouldBeCalledTimes($turnBeastFirstTimes);
        $this->turn->make($this->orderus->reveal(), $this->wildBeast->reveal())
            ->shouldBeCalledTimes($turnOrderusFirstTimes);

        $gameplay = new GamePlayService(
            $this->turn->reveal(),
            $this->factory->reveal(),
            $this->attackResolver->reveal(),
            $this->mediatorFactory->reveal()
        );

        $gameplay->setMediator($this->mediator->reveal());
        $this->mediatorFactory->createMediatorForColleagues(
            $this->wildBeast->reveal(),
            $this->orderus->reveal(),
            $this->attackResolver->reveal(),
            $this->turn->reveal(),
            $gameplay
        )->willReturn($this->mediator->reveal());

        $gameplay->startBattle();
    }
}

abstract class UnitColleague implements UnitInterface, ColleagueInterface
{
}
