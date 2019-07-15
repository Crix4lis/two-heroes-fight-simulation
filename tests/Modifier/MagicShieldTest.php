<?php
declare(strict_types=1);

namespace Test\Emagia\Modifier;

use Emagia\MediatorPattern\EventAndLogsMediatorInterface;
use Emagia\Modifier\MagicShield;
use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Luck;
use Emagia\Property\Speed;
use Emagia\Property\Strength;
use Emagia\Randomizer\RandomizerInterface;
use Emagia\Unit\Unit;
use PHPUnit\Framework\TestCase;

class MagicShieldTest extends TestCase
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
     * @var \Emagia\Randomizer\RandomizerInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $randomizer;
    /**
     * @var \Emagia\MediatorPattern\EventAndLogsMediatorInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $mediator;

    public function setUp(): void
    {
        $this->strength = $this->prophesize(Strength::class);
        $this->defence = $this->prophesize(Defence::class);
        $this->healthPoints = $this->prophesize(HealthPoints::class);
        $this->speed = $this->prophesize(Speed::class);
        $this->luck = $this->prophesize(Luck::class);
        $this->randomizer = $this->prophesize(RandomizerInterface::class);
        $this->mediator = $this->prophesize(EventAndLogsMediatorInterface::class);
    }

    public function unitsStatsForMagicShieldUsageDataProvider(): array
    {
        //att: 60 - 90
        //def: 45 - 90
        return [// hpa; att;hpd; def;left
            'def less than attack' => [100, 90, 100, 50, 80],
            'kills' => [100, 90, 20, 50, 0],
            'would kill without shield' => [100, 90, 30, 45, 7],
            'already dead' => [100, 90, 0, 50, 0],
        ];
    }

    public function unitsStatsForMagicShieldNotUsageDataProvider(): array
    {
        //att: 60 - 90
        //def: 45 - 90
        return [// hpa; att;hpd; def;left
            'def less than attack' => [100, 90, 100, 50, 60],
            'kills' => [100, 90, 20, 50, 0],
            'would not kill with shield' => [100, 90, 30, 45, 0],
            'already dead' => [100, 90, 0, 50, 0],
        ];
    }

    /**
     * @dataProvider unitsStatsForMagicShieldUsageDataProvider
     *
     * @param int $attackerHp
     * @param int $attackPts
     * @param int $defenderHp
     * @param int $defendPts
     * @param int $expectedDefenderHpLeft
     */
    public function testUsesMagicShieldToDefend(
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

        $attacker = new Unit(
            'name',
            new HealthPoints($attackerHp),
            new Strength($attackPts),
            $this->defence->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal()
        );
        $attacker->setMediator($this->mediator->reveal());

        $defender = new MagicShield(new Unit(
            'name',
            new HealthPoints($defenderHp),
            $this->strength->reveal(),
            new Defence($defendPts),
            $this->speed->reveal(),
            $this->luck->reveal()
        ), $this->randomizer->reveal());
        $defender->setMediator($this->mediator->reveal());

        $attacker->performAttack($defender);

        $this->assertEquals($attackerHp, $attacker->getCurrentHealth()->getPoints());
        $this->assertEquals($expectedDefenderHpLeft, $defender->getCurrentHealth()->getPoints());
    }

    /**
     * @dataProvider unitsStatsForMagicShieldNotUsageDataProvider
     *
     * @param int $attackerHp
     * @param int $attackPts
     * @param int $defenderHp
     * @param int $defendPts
     * @param int $expectedDefenderHpLeft
     */
    public function testDoesNotUseMagicShieldToDefend(
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

        $attacker = new Unit(
            'name',
            new HealthPoints($attackerHp),
            new Strength($attackPts),
            $this->defence->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal()
        );
        $attacker->setMediator($this->mediator->reveal());

        $defender = new MagicShield(new Unit(
            'name',
            new HealthPoints($defenderHp),
            $this->strength->reveal(),
            new Defence($defendPts),
            $this->speed->reveal(),
            $this->luck->reveal()
        ), $this->randomizer->reveal());
        $defender->setMediator($this->mediator->reveal());

        $attacker->performAttack($defender);

        $this->assertEquals($attackerHp, $attacker->getCurrentHealth()->getPoints());
        $this->assertEquals($expectedDefenderHpLeft, $defender->getCurrentHealth()->getPoints());
    }
}
