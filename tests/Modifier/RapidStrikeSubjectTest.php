<?php
declare(strict_types=1);

namespace Test\Emagia\Modifier;

use Emagia\Event\BlockedDamageEvent;
use Emagia\Event\PerformedAttackEvent;
use Emagia\Event\RapidStrikeUsedEvent;
use Emagia\Event\ReceivedDamageEvent;
use Emagia\Event\UnitDiedEvent;
use Emagia\Modifier\RapidStrike;
use Emagia\ObserverPattern\ObserverInterface;
use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Luck;
use Emagia\Property\Speed;
use Emagia\Property\Strength;
use Emagia\Randomizer\RandomizerInterface;
use Emagia\Unit\Unit;
use PHPUnit\Framework\TestCase;

class RapidStrikeSubjectTest extends TestCase
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

    public function unitsStatsForRapidStrikeUsageDataProvider(): array
    {
        //att: 60 - 90
        //def: 40 - 60
        return [// hpa; att;hpd; def;left
            'def less than attack' => [100, 90, 100, 60, 40],
        ];
    }

    /**
     * @dataProvider unitsStatsForRapidStrikeUsageDataProvider
     *
     * @param int $attackerHp
     * @param int $attackPts
     * @param int $defenderHp
     * @param int $defendPts
     * @param int $expectedDefenderHpLeft
     */
    public function testAttacksDefenderWithRapidStrikeAndChecksIfObserverIsNotified(
        int $attackerHp,
        int $attackPts,
        int $defenderHp,
        int $defendPts,
        int $expectedDefenderHpLeft
    ): void {
        $this->randomizer->randomize(1, 100)->willReturn(10);
        $this->strength->getPoints()->willReturn(1);
        $this->defence->getPoints()->willReturn(1);
        $this->speed->getPoints()->willReturn(1);
        $this->luck->getPoints()->willReturn(1);
        $attackerName = 'attacker';
        $defenderName = 'defender';

        $this->observer->update(new PerformedAttackEvent($attackerName, $attackPts))->shouldBeCalledTimes(2);
        $this->observer->update(new RapidStrikeUsedEvent($attackerName))->shouldBeCalled();
        $this->observer->update(new BlockedDamageEvent($defenderName, $defendPts))->shouldBeCalled();
        $this->observer->update(
            new ReceivedDamageEvent(
                $defenderName,
                $attackPts - $defendPts,
                $defenderHp - ($attackPts - $defendPts)
            )
        )->shouldBeCalledTimes(1);
        $this->observer->update(
            new ReceivedDamageEvent(
                $defenderName,
                $attackPts - $defendPts,
                $defenderHp - 2*($attackPts - $defendPts)
            )
        )->shouldBeCalledTimes(1);

        $attacker = new RapidStrike(new Unit(
            $attackerName,
            new HealthPoints($attackerHp),
            new Strength($attackPts),
            $this->defence->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal()
        ),$this->randomizer->reveal());
        $attacker->register($this->observer->reveal());

        $defender = new Unit(
            $defenderName,
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

    public function unitsStatsForRapidStrikeUsageAndDefenderKillDataProvider(): array
    {
        //att: 60 - 90
        //def: 40 - 60
        return [// hpa; att;hpd; def;left
            'kills with second attack' => [100, 90, 70, 50, 0],
        ];
    }

    /**
     * @dataProvider unitsStatsForRapidStrikeUsageAndDefenderKillDataProvider
     *
     * @param int $attackerHp
     * @param int $attackPts
     * @param int $defenderHp
     * @param int $defendPts
     * @param int $expectedDefenderHpLeft
     */
    public function testAttacksDefenderWithRapidStrikeKillsDefenderAndChecksIfObserverIsNotified(
        int $attackerHp,
        int $attackPts,
        int $defenderHp,
        int $defendPts,
        int $expectedDefenderHpLeft
    ): void {
        $this->randomizer->randomize(1, 100)->willReturn(10);
        $this->strength->getPoints()->willReturn(1);
        $this->defence->getPoints()->willReturn(1);
        $this->speed->getPoints()->willReturn(1);
        $this->luck->getPoints()->willReturn(1);
        $attackerName = 'attacker';
        $defenderName = 'defender';

        $this->observer->update(new PerformedAttackEvent($attackerName, $attackPts))->shouldBeCalledTimes(2);
        $this->observer->update(new RapidStrikeUsedEvent($attackerName))->shouldBeCalled();
        $this->observer->update(new BlockedDamageEvent($defenderName, $defendPts))->shouldBeCalled();
        $this->observer->update(
            new ReceivedDamageEvent(
                $defenderName,
                $attackPts - $defendPts,
                $defenderHp - ($attackPts - $defendPts)
            )
        )->shouldBeCalledTimes(1);
        $this->observer->update(
            new ReceivedDamageEvent(
                $defenderName,
                $attackPts - $defendPts,
                0
            )
        )->shouldBeCalledTimes(1);

        $this->observer->update(new UnitDiedEvent($defenderName))->shouldBeCalled();

        $attacker = new RapidStrike(new Unit(
            $attackerName,
            new HealthPoints($attackerHp),
            new Strength($attackPts),
            $this->defence->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal()
        ),$this->randomizer->reveal());
        $attacker->register($this->observer->reveal());

        $defender = new Unit(
            $defenderName,
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

    public function testWouldUseRapidStrikeButDefenderIsKilledWithFirstAttack(): void
    {
        $this->randomizer->randomize(1, 100)->willReturn(10);
        $this->strength->getPoints()->willReturn(1);
        $this->defence->getPoints()->willReturn(1);
        $this->speed->getPoints()->willReturn(1);
        $this->luck->getPoints()->willReturn(1);
        $attackerName = 'attacker';
        $defenderName = 'defender';
        $attackPts = 90;
        $defendPts = 60;
        $attackerHp = 100;
        $defenderHp = 30;

        $this->observer->update(new PerformedAttackEvent($attackerName, $attackPts))->shouldBeCalledTimes(1);
        $this->observer->update(new RapidStrikeUsedEvent($attackerName))->shouldNotBeCalled();
        $this->observer->update(new BlockedDamageEvent($defenderName, $defendPts))->shouldBeCalled();
        $this->observer->update(
            new ReceivedDamageEvent(
                $defenderName,
                $attackPts - $defendPts,
                0
            )
        )->shouldBeCalledTimes(1);
        $this->observer->update(new UnitDiedEvent($defenderName))->shouldBeCalled();

        $attacker = new RapidStrike(new Unit(
            $attackerName,
            new HealthPoints($attackerHp),
            new Strength($attackPts),
            $this->defence->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal()
        ),$this->randomizer->reveal());
        $attacker->register($this->observer->reveal());

        $defender = new Unit(
            $defenderName,
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

    public function unitsStatsForRapidStrikeNotUsedDataProvider(): array
    {
        //att: 60 - 90
        //def: 45 - 90
        return [// hpa; att;hpd; def;left
            'def less than attack' => [100, 90, 100, 60, 70],
            'would kill with second attack' => [100, 90, 70, 50, 30],
        ];
    }

    /**
     * @dataProvider unitsStatsForRapidStrikeNotUsedDataProvider
     *
     * @param int $attackerHp
     * @param int $attackPts
     * @param int $defenderHp
     * @param int $defendPts
     * @param int $expectedDefenderHpLeft
     */
    public function testAttacksDefenderWithoutRapidStrikeAndChecksIfObserverIsNotified(
        int $attackerHp,
        int $attackPts,
        int $defenderHp,
        int $defendPts,
        int $expectedDefenderHpLeft
    ): void {
        $this->randomizer->randomize(1, 100)->willReturn(11);
        $this->strength->getPoints()->willReturn(1);
        $this->defence->getPoints()->willReturn(1);
        $this->speed->getPoints()->willReturn(1);
        $this->luck->getPoints()->willReturn(1);
        $attackerName = 'attacker';
        $defenderName = 'defender';
        $reducedToByShield = $attackPts - $defendPts;

        $this->observer->update(new PerformedAttackEvent($attackerName, $attackPts))->shouldBeCalledTimes(1);
        $this->observer->update(new BlockedDamageEvent($defenderName, $defendPts))->shouldBeCalled();
        $this->observer->update(new RapidStrikeUsedEvent($attackerName))->shouldNotBeCalled();
        $this->observer->update(
            new ReceivedDamageEvent(
                $defenderName,
                $attackPts - $defendPts,
                $defenderHp - $reducedToByShield
            )
        )->shouldBeCalledTimes(1);

        $attacker = new RapidStrike(new Unit(
            $attackerName,
            new HealthPoints($attackerHp),
            new Strength($attackPts),
            $this->defence->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal()
        ), $this->randomizer->reveal());
        $attacker->register($this->observer->reveal());

        $defender = new Unit(
            $defenderName,
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

    public function testAttacksDefenderWithoutRapidStrikeKillsDefenderAndChecksIfObserverIsNotified(): void
    {
        $attackerHp = 100;
        $attackPts = 90;
        $defenderHp = 20;
        $defendPts = 50;

        $this->randomizer->randomize(1, 100)->willReturn(11);
        $this->strength->getPoints()->willReturn(1);
        $this->defence->getPoints()->willReturn(1);
        $this->speed->getPoints()->willReturn(1);
        $this->luck->getPoints()->willReturn(1);
        $attackerName = 'attacker';
        $defenderName = 'defender';
        $reducedToByShield = $attackPts - $defendPts;

        $this->observer->update(new PerformedAttackEvent($attackerName, $attackPts))->shouldBeCalledTimes(1);
        $this->observer->update(new BlockedDamageEvent($defenderName, $defendPts))->shouldBeCalled();
        $this->observer->update(new RapidStrikeUsedEvent($attackerName))->shouldNotBeCalled();
        $this->observer->update(
            new ReceivedDamageEvent($defenderName, $reducedToByShield, 0)
        )->shouldBeCalled();
        $this->observer->update(new UnitDiedEvent($defenderName))->shouldBeCalled();

        $attacker = new RapidStrike(new Unit(
            $attackerName,
            new HealthPoints($attackerHp),
            new Strength($attackPts),
            $this->defence->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal()
        ), $this->randomizer->reveal());
        $attacker->register($this->observer->reveal());

        $defender = new Unit(
            $defenderName,
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
}
