<?php
declare(strict_types=1);

namespace Test\Emagia;

use Emagia\AttackerResolver;
use Emagia\AttackResolverException;
use Emagia\MediatorPattern\EventAndLogsMediatorInterface;
use Emagia\Property\Luck;
use Emagia\Property\Speed;
use Emagia\Unit\UnitInterface;
use function get_class;
use PHPUnit\Framework\TestCase;

class AttackResolverTest extends TestCase
{
    /**
     * @var \Emagia\Property\Luck|\Prophecy\Prophecy\ObjectProphecy
     */
    private $luck;
    /**
     * @var \Emagia\Property\Speed|\Prophecy\Prophecy\ObjectProphecy
     */
    private $speed;
    /**
     * @var \Emagia\Unit\UnitInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $secondUnit;
    /**
     * @var \Emagia\Unit\UnitInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $firstUnit;
    /**
     * @var \Emagia\MediatorPattern\EventAndLogsMediatorInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $mediator;

    public function setUp(): void
    {
        $this->firstUnit = $this->prophesize(UnitInterface::class);
        $this->secondUnit = $this->prophesize(UnitInterface::class);
        $this->speed = $this->prophesize(Speed::class);
        $this->luck = $this->prophesize(Luck::class);
        $this->mediator = $this->prophesize(EventAndLogsMediatorInterface::class);
    }

    public function unitsPropertiesForFirstUnitAsAttackerProvider(): array
    {
        return [
            'by speed' => [false, true, 1, false, false, 0],
            'by luck' => [true, false, 0, false, true, 1],
        ];
    }

    /**
     * @dataProvider unitsPropertiesForFirstUnitAsAttackerProvider
     *
     * @param bool $speedEquals
     * @param bool $speedGreater
     * @param int  $timesSpeedIsGreater
     * @param bool $luckEquals
     * @param bool $luckGreater
     * @param int  $timesLuckIsGreater
     */
    public function testResolvesFirstUnitAsAttacker(
        bool $speedEquals,
        bool $speedGreater,
        int $timesSpeedIsGreater,
        bool $luckEquals,
        bool $luckGreater,
        int $timesLuckIsGreater
    ): void
    {
        $this->firstUnit->getSpeed()->willReturn($this->speed->reveal());
        $this->secondUnit->getSpeed()->willReturn($this->speed->reveal());
        $this->firstUnit->getLuck()->willReturn($this->luck->reveal());
        $this->secondUnit->getLuck()->willReturn($this->luck->reveal());
        $this->speed->isEqual($this->speed->reveal())->willReturn($speedEquals)->shouldBeCalledTimes(1);
        $this->speed->isGreater($this->speed)->willReturn($speedGreater)->shouldBeCalledTimes($timesSpeedIsGreater);
        $this->luck->isEqual($this->luck->reveal())->willReturn($luckEquals)->shouldBeCalledTimes($timesLuckIsGreater);
        $this->luck->isGreater($this->luck)->willReturn($luckGreater)->shouldBeCalledTimes($timesLuckIsGreater);

        $resolver = new AttackerResolver();
        $resolver->setMediator($this->mediator->reveal());
        $attacker = $resolver->resolveAttacker($this->firstUnit->reveal(), $this->secondUnit->reveal());

        $this->assertEquals(get_class($this->firstUnit->reveal()), get_class($attacker));
    }

    public function unitsPropertiesForSecondUnitAsAttackerProvider(): array
    {
        return [
            'by speed' => [false, false, 1, false, false, 0],
            'by luck' => [true, false, 0, false, false, 1],
        ];
    }

    /**
     * @dataProvider unitsPropertiesForSecondUnitAsAttackerProvider
     *
     * @param bool $speedEquals
     * @param bool $speedGreater
     * @param int  $timesSpeedIsGreater
     * @param bool $luckEquals
     * @param bool $luckGreater
     * @param int  $timesLuckIsGreater
     */
    public function testResolvesSecondUnitAsAttacker(
        bool $speedEquals,
        bool $speedGreater,
        int $timesSpeedIsGreater,
        bool $luckEquals,
        bool $luckGreater,
        int $timesLuckIsGreater
    ): void
    {
        $this->firstUnit->getSpeed()->willReturn($this->speed->reveal());
        $this->secondUnit->getSpeed()->willReturn($this->speed->reveal());
        $this->firstUnit->getLuck()->willReturn($this->luck->reveal());
        $this->secondUnit->getLuck()->willReturn($this->luck->reveal());
        $this->speed->isEqual($this->speed->reveal())->willReturn($speedEquals)->shouldBeCalledTimes(1);
        $this->speed->isGreater($this->speed)->willReturn($speedGreater)->shouldBeCalledTimes($timesSpeedIsGreater);
        $this->luck->isEqual($this->luck->reveal())->willReturn($luckEquals)->shouldBeCalledTimes($timesLuckIsGreater);
        $this->luck->isGreater($this->luck)->willReturn($luckGreater)->shouldBeCalledTimes($timesLuckIsGreater);

        $resolver = new AttackerResolver();
        $resolver->setMediator($this->mediator->reveal());
        $attacker = $resolver->resolveAttacker($this->firstUnit->reveal(), $this->secondUnit->reveal());

        $this->assertEquals(get_class($this->secondUnit->reveal()), get_class($attacker));
    }

    public function testThrowsExceptionWhenLuckAndSpeedIsEqual(): void
    {
        $this->expectException(AttackResolverException::class);

        $this->firstUnit->getSpeed()->willReturn($this->speed->reveal());
        $this->secondUnit->getSpeed()->willReturn($this->speed->reveal());
        $this->firstUnit->getLuck()->willReturn($this->luck->reveal());
        $this->secondUnit->getLuck()->willReturn($this->luck->reveal());
        $this->speed->isEqual($this->speed->reveal())->willReturn(true)->shouldBeCalledTimes(1);
        $this->speed->isGreater($this->speed)->shouldNotBeCalled();
        $this->luck->isEqual($this->luck->reveal())->willReturn(true)->shouldBeCalledTimes(1);
        $this->luck->isGreater($this->luck)->shouldNotBeCalled();

        $resolver = new AttackerResolver();
        $resolver->setMediator($this->mediator->reveal());
        $resolver->resolveAttacker($this->firstUnit->reveal(), $this->secondUnit->reveal());
    }
}
