<?php
declare(strict_types=1);

namespace Test\Emagia\Unit;

use Emagia\Event\BlockedDamageEvent;
use Emagia\Event\DefenderAlredyDeadEvent;
use Emagia\Event\PerformedAttackEvent;
use Emagia\Event\ReceivedDamageEvent;
use Emagia\Event\UnitDiedEvent;
use Emagia\ObserverPattern\ObserverInterface;
use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Luck;
use Emagia\Property\Speed;
use Emagia\Property\Strength;
use Emagia\Unit\Unit;
use PHPUnit\Framework\TestCase;

class UnitSubjectTest extends TestCase
{
    /**
     * @var \Emagia\Property\HealthPoints|\Prophecy\Prophecy\ObjectProphecy
     */
    private $healthPoints;
    /**
     * @var \Emagia\Property\Defence|\Prophecy\Prophecy\ObjectProphecy
     */
    private $defence;
    /**
     * @var \Emagia\Property\Strength|\Prophecy\Prophecy\ObjectProphecy
     */
    private $strength;
    /**
     * @var \Emagia\Property\Speed|\Prophecy\Prophecy\ObjectProphecy
     */
    private $speed;
    /**
     * @var \Emagia\Property\Luck|\Prophecy\Prophecy\ObjectProphecy
     */
    private $luck;
    /**
     * @var \Emagia\ObserverPattern\ObserverInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $observer;

    public function setUp(): void
    {
        $this->strength = $this->prophesize(Strength::class);
        $this->defence = $this->prophesize(Defence::class);
        $this->healthPoints = $this->prophesize(HealthPoints::class);
        $this->speed = $this->prophesize(Speed::class);
        $this->luck = $this->prophesize(Luck::class);
        $this->observer = $this->prophesize(ObserverInterface::class);
    }

    public function unitsStatsDataProvider(): array
    {
        //att: 60 - 90
        //def: 45 - 90
        return [// hpa; att;hpd; def;left
            'def less than attack' => [100, 90, 100, 50, 60],
        ];
    }

    /**
     * @dataProvider unitsStatsDataProvider
     *
     * @param int $attackerHp
     * @param int $attackPts
     * @param int $defenderHp
     * @param int $defendPts
     * @param int $expectedDefenderHpLeft
     */
    public function testDefendsFromAttackerAndChecksIfObserverIsNotified(
        int $attackerHp,
        int $attackPts,
        int $defenderHp,
        int $defendPts,
        int $expectedDefenderHpLeft
    ): void {
        $this->strength->getPoints()->willReturn(1);
        $this->defence->getPoints()->willReturn(1);
        $this->speed->getPoints()->willReturn(1);
        $this->luck->getPoints()->willReturn(1);
        $this->observer->update(new PerformedAttackEvent('attacker', $attackPts))->shouldBeCalled();
        $this->observer->update(new BlockedDamageEvent('defender', $defendPts))->shouldBeCalled();
        $this->observer->update(
            new ReceivedDamageEvent(
                'defender',
                $attackPts - $defendPts,
                $defenderHp - ($attackPts - $defendPts)
            )
        )->shouldBeCalledTimes(1);

        $attacker = new Unit(
            'attacker',
            new HealthPoints($attackerHp),
            new Strength($attackPts),
            $this->defence->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal()
        );
        $attacker->register($this->observer->reveal());

        $defender = new Unit(
            'defender',
            new HealthPoints($defenderHp),
            $this->strength->reveal(),
            new Defence($defendPts),
            $this->speed->reveal(),
            $this->luck->reveal()
        );
        $defender->register($this->observer->reveal());
        $attacker->performAttack($defender);

        $this->assertEquals($attackerHp, $attacker->getCurrentHealth()->getPoints());
        $this->assertEquals($expectedDefenderHpLeft, $defender->getCurrentHealth()->getPoints());
    }

    public function testDefendsFromAttackerAndDefenderDiesAndChecksIfObserverIsNotified(): void {
        $this->strength->getPoints()->willReturn(1);
        $this->defence->getPoints()->willReturn(1);
        $this->speed->getPoints()->willReturn(1);
        $this->luck->getPoints()->willReturn(1);
        $attackerHp = 100;
        $defenderHp = 30;
        $attackPts = 90;
        $defendPts = 50;

        $this->observer->update(new PerformedAttackEvent('attacker', $attackPts))->shouldBeCalled();
        $this->observer->update(new BlockedDamageEvent('defender', $defendPts))->shouldBeCalled();
        $this->observer->update(
            new ReceivedDamageEvent(
                'defender',
                $attackPts - $defendPts,
                0
            )
        )->shouldBeCalledTimes(1);
        $this->observer->update(new UnitDiedEvent('defender'))->shouldBeCalled();

        $attacker = new Unit(
            'attacker',
            new HealthPoints($attackerHp),
            new Strength($attackPts),
            $this->defence->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal()
        );
        $attacker->register($this->observer->reveal());

        $defender = new Unit(
            'defender',
            new HealthPoints($defenderHp),
            $this->strength->reveal(),
            new Defence($defendPts),
            $this->speed->reveal(),
            $this->luck->reveal()
        );
        $defender->register($this->observer->reveal());
        $attacker->performAttack($defender);

        $this->assertEquals($attackerHp, $attacker->getCurrentHealth()->getPoints());
        $this->assertEquals(0, $defender->getCurrentHealth()->getPoints());
    }

    public function testChecksIfObserverIsNotifiedThatDefenderIsAlreadyDead(): void
    {
        $attackPts = 90;
        $defendPts = 50;
        $attackerHp = 100;
        $defenderHp = 0;

        $this->strength->getPoints()->willReturn(1);
        $this->defence->getPoints()->willReturn(1);
        $this->speed->getPoints()->willReturn(1);
        $this->luck->getPoints()->willReturn(1);

        $attacker = new Unit(
            'attacker',
            new HealthPoints($attackerHp),
            new Strength($attackPts),
            $this->defence->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal()
        );
        $attacker->register($this->observer->reveal());

        $defender = new Unit(
            'defender',
            new HealthPoints($defenderHp),
            $this->strength->reveal(),
            new Defence($defendPts),
            $this->speed->reveal(),
            $this->luck->reveal()
        );

        $this->observer->update(new DefenderAlredyDeadEvent($attacker, $defender))->shouldBeCalled();

        $attacker->performAttack($defender);

        $this->assertEquals($attackerHp, $attacker->getCurrentHealth()->getPoints());
        $this->assertEquals(0, $defender->getCurrentHealth()->getPoints());
    }
}
