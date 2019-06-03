<?php
declare(strict_types=1);

namespace Test\Emagia;

use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Luck;
use Emagia\Property\Speed;
use Emagia\Property\Strength;
use Emagia\Unit\Unit;
use PHPUnit\Framework\TestCase;

class UnitTest extends TestCase
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

    public function setUp(): void
    {
        $this->strength = $this->prophesize(Strength::class);
        $this->defence = $this->prophesize(Defence::class);
        $this->healthPoints = $this->prophesize(HealthPoints::class);
        $this->speed = $this->prophesize(Speed::class);
        $this->luck = $this->prophesize(Luck::class);
    }

    public function unitsStatsDataProvider(): array
    {
        //att: 60 - 90
        //def: 45 - 90
        return [// hpa; att;hpd; def;left
            'def less than attack' => [100, 90, 100, 50, 60],
            'def greater than attack' => [100, 60, 100, 90, 100],
            'kills' => [100, 90, 30, 50, 0],
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
    public function testDefendsFromAttacker(
        int $attackerHp,
        int $attackPts,
        int $defenderHp,
        int $defendPts,
        int $expectedDefenderHpLeft
    ): void {

        $attacker = new Unit(
            new HealthPoints($attackerHp),
            new Strength($attackPts),
            $this->defence->reveal(),
            $this->speed->reveal(),
            $this->luck->reveal()
        );

        $defender = new Unit(
            new HealthPoints($defenderHp),
            $this->strength->reveal(),
            new Defence($defendPts),
            $this->speed->reveal(),
            $this->luck->reveal()
        );

        $attacker->performAttack($defender);

        $this->assertEquals($attackerHp, $attacker->getCurrentHealth()->getPoints());
        $this->assertEquals($expectedDefenderHpLeft, $defender->getCurrentHealth()->getPoints());
    }
}
