<?php
declare(strict_types=1);

namespace Test\Emagia\Modifier;

use Emagia\Event\BlockedDamageEvent;
use Emagia\Event\MagicShieldUsedEvent;
use Emagia\Event\PerformedAttackEvent;
use Emagia\Event\ReceivedDamageEvent;
use Emagia\Event\UnitDiedEvent;
use Emagia\Modifier\MagicShield;
use Emagia\ObserverPattern\ObserverInterface;
use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Luck;
use Emagia\Property\Speed;
use Emagia\Property\Strength;
use Emagia\Randomizer\RandomizerInterface;
use Emagia\Unit\Unit;
use PHPUnit\Framework\TestCase;

class MagicShieldSubjectTest extends TestCase
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
    /**
     * @var \Emagia\Randomizer\RandomizerInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $randomizer;

    public function setUp(): void
    {
        $this->strength = $this->prophesize(Strength::class);
        $this->defence = $this->prophesize(Defence::class);
        $this->healthPoints = $this->prophesize(HealthPoints::class);
        $this->speed = $this->prophesize(Speed::class);
        $this->luck = $this->prophesize(Luck::class);
        $this->observer = $this->prophesize(ObserverInterface::class);
        $this->randomizer = $this->prophesize(RandomizerInterface::class);
    }

    public function unitsStatsForShieldUsageDataProvider(): array
    {
        //att: 60 - 90
        //def: 45 - 90
        return [// hpa; att;hpd; def;left
            'def less than attack' => [100, 90, 100, 50, 80],
            'would kill without shield' => [100, 90, 30, 50, 10],
        ];
    }

    /**
     * @dataProvider unitsStatsForShieldUsageDataProvider
     *
     * @param int $attackerHp
     * @param int $attackPts
     * @param int $defenderHp
     * @param int $defendPts
     * @param int $expectedDefenderHpLeft
     */
    public function testDefendsWithMagicShieldFromAttackerAndChecksIfObserverIsNotified(
        int $attackerHp,
        int $attackPts,
        int $defenderHp,
        int $defendPts,
        int $expectedDefenderHpLeft
    ): void {
        $this->randomizer->randomize(1, 100)->willReturn(20);
        $this->strength->getPoints()->willReturn(1);
        $this->defence->getPoints()->willReturn(1);
        $this->speed->getPoints()->willReturn(1);
        $this->luck->getPoints()->willReturn(1);
        $attackerName = 'attacker';
        $defenderName = 'defender';
        $reducedToByShield = (int)round(($attackPts - $defendPts) / 2, 0);

        $this->observer->update(new PerformedAttackEvent($attackerName, $attackPts))->shouldBeCalled();
        $this->observer->update(new BlockedDamageEvent($defenderName, $defendPts))->shouldBeCalled();
        $this->observer->update(new MagicShieldUsedEvent($defenderName, $reducedToByShield))->shouldBeCalled();
        $this->observer->update(
            new ReceivedDamageEvent($defenderName, $reducedToByShield)
        )->shouldBeCalled();

        $attacker = new Unit(
            $attackerName,
            new HealthPoints($attackerHp),
            new Strength($attackPts),
            $this->defence->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal()
        );
        $attacker->register($this->observer->reveal());

        $defender = new MagicShield(new Unit(
            $defenderName,
            new HealthPoints($defenderHp),
            $this->strength->reveal(),
            new Defence($defendPts),
            $this->speed->reveal(),
            $this->luck->reveal()
        ), $this->randomizer->reveal());

        $defender->register($this->observer->reveal());
        $attacker->performAttack($defender);

        $this->assertEquals($attackerHp, $attacker->getCurrentHealth()->getPoints());
        $this->assertEquals($expectedDefenderHpLeft, $defender->getCurrentHealth()->getPoints());
    }

    public function testDefendsWithMagicShieldFromAttackerButDiesAndChecksIfObserverIsNotified(): void {
        $attackerHp = 100;
        $attackPts = 90;
        $defenderHp = 20;
        $defendPts = 50;

        $this->randomizer->randomize(1, 100)->willReturn(20);
        $this->strength->getPoints()->willReturn(1);
        $this->defence->getPoints()->willReturn(1);
        $this->speed->getPoints()->willReturn(1);
        $this->luck->getPoints()->willReturn(1);
        $attackerName = 'attacker';
        $defenderName = 'defender';
        $reducedToByShield = (int)round(($attackPts - $defendPts) / 2, 0);

        $this->observer->update(new PerformedAttackEvent($attackerName, $attackPts))->shouldBeCalled();
        $this->observer->update(new BlockedDamageEvent($defenderName, $defendPts))->shouldBeCalled();
        $this->observer->update(new MagicShieldUsedEvent($defenderName, $reducedToByShield))->shouldBeCalled();
        $this->observer->update(
            new ReceivedDamageEvent($defenderName, $reducedToByShield)
        )->shouldBeCalled();
        $this->observer->update(new UnitDiedEvent($defenderName))->shouldBeCalled();

        $attacker = new Unit(
            $attackerName,
            new HealthPoints($attackerHp),
            new Strength($attackPts),
            $this->defence->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal()
        );
        $attacker->register($this->observer->reveal());

        $defender = new MagicShield(new Unit(
            $defenderName,
            new HealthPoints($defenderHp),
            $this->strength->reveal(),
            new Defence($defendPts),
            $this->speed->reveal(),
            $this->luck->reveal()
        ), $this->randomizer->reveal());

        $defender->register($this->observer->reveal());
        $attacker->performAttack($defender);

        $this->assertEquals($attackerHp, $attacker->getCurrentHealth()->getPoints());
        $this->assertEquals(0, $defender->getCurrentHealth()->getPoints());
    }

    public function unitsStatsForShieldNotUsageDataProvider(): array
    {
        //att: 60 - 90
        //def: 45 - 90
        return [// hpa; att;hpd; def;left
            'def less than attack' => [100, 90, 100, 50, 60],
        ];
    }

    /**
     * @dataProvider unitsStatsForShieldNotUsageDataProvider
     *
     * @param int $attackerHp
     * @param int $attackPts
     * @param int $defenderHp
     * @param int $defendPts
     * @param int $expectedDefenderHpLeft
     */
    public function testDefendsWithoutMagicShieldFromAttackerAndChecksIfObserverIsNotified(
        int $attackerHp,
        int $attackPts,
        int $defenderHp,
        int $defendPts,
        int $expectedDefenderHpLeft
    ): void {
        $this->randomizer->randomize(1, 100)->willReturn(21);
        $this->strength->getPoints()->willReturn(1);
        $this->defence->getPoints()->willReturn(1);
        $this->speed->getPoints()->willReturn(1);
        $this->luck->getPoints()->willReturn(1);
        $attackerName = 'attacker';
        $defenderName = 'defender';
        $reducedToByShield = $attackPts - $defendPts;

        $this->observer->update(new PerformedAttackEvent($attackerName, $attackPts))->shouldBeCalled();
        $this->observer->update(new BlockedDamageEvent($defenderName, $defendPts))->shouldBeCalled();
        $this->observer->update(new MagicShieldUsedEvent($defenderName, $reducedToByShield))->shouldNotBeCalled();
        $this->observer->update(
            new ReceivedDamageEvent($defenderName, $reducedToByShield)
        )->shouldBeCalled();

        $attacker = new Unit(
            $attackerName,
            new HealthPoints($attackerHp),
            new Strength($attackPts),
            $this->defence->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal()
        );
        $attacker->register($this->observer->reveal());

        $defender = new MagicShield(new Unit(
            $defenderName,
            new HealthPoints($defenderHp),
            $this->strength->reveal(),
            new Defence($defendPts),
            $this->speed->reveal(),
            $this->luck->reveal()
        ), $this->randomizer->reveal());

        $defender->register($this->observer->reveal());
        $attacker->performAttack($defender);

        $this->assertEquals($attackerHp, $attacker->getCurrentHealth()->getPoints());
        $this->assertEquals($expectedDefenderHpLeft, $defender->getCurrentHealth()->getPoints());
    }

    public function unitsStatsForShieldNotUsageAndDyingDataProvider(): array
    {
        return [// hpa; att;hpd; def;left
            'would kill without shield' => [100, 90, 30, 50, 0],
            'kills' => [100, 90, 20, 50, 0],
        ];
    }

    /**
     * @dataProvider unitsStatsForShieldNotUsageAndDyingDataProvider
     *
     * @param int $attackerHp
     * @param int $attackPts
     * @param int $defenderHp
     * @param int $defendPts
     * @param int $expectedDefenderHpLeft
     */
    public function testDefendsWithoutMagicShieldFromAttackerButDiesAndChecksIfObserverIsNotified(
        int $attackerHp,
        int $attackPts,
        int $defenderHp,
        int $defendPts,
        int $expectedDefenderHpLeft
    ): void {
        $this->randomizer->randomize(1, 100)->willReturn(21);
        $this->strength->getPoints()->willReturn(1);
        $this->defence->getPoints()->willReturn(1);
        $this->speed->getPoints()->willReturn(1);
        $this->luck->getPoints()->willReturn(1);
        $attackerName = 'attacker';
        $defenderName = 'defender';
        $reducedToByShield = $attackPts - $defendPts;

        $this->observer->update(new PerformedAttackEvent($attackerName, $attackPts))->shouldBeCalled();
        $this->observer->update(new BlockedDamageEvent($defenderName, $defendPts))->shouldBeCalled();
        $this->observer->update(new MagicShieldUsedEvent($defenderName, $reducedToByShield))->shouldNotBeCalled();
        $this->observer->update(
            new ReceivedDamageEvent($defenderName, $reducedToByShield)
        )->shouldBeCalled();
        $this->observer->update(new UnitDiedEvent($defenderName))->shouldBeCalled();

        $attacker = new Unit(
            $attackerName,
            new HealthPoints($attackerHp),
            new Strength($attackPts),
            $this->defence->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal()
        );
        $attacker->register($this->observer->reveal());

        $defender = new MagicShield(new Unit(
            $defenderName,
            new HealthPoints($defenderHp),
            $this->strength->reveal(),
            new Defence($defendPts),
            $this->speed->reveal(),
            $this->luck->reveal()
        ), $this->randomizer->reveal());

        $defender->register($this->observer->reveal());
        $attacker->performAttack($defender);

        $this->assertEquals($attackerHp, $attacker->getCurrentHealth()->getPoints());
        $this->assertEquals($expectedDefenderHpLeft, $defender->getCurrentHealth()->getPoints());
    }
}
