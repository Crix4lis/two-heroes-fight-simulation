<?php
declare(strict_types=1);

namespace Test\Emagia\Unit;

use Emagia\Modifier\MagicShield;
use Emagia\Modifier\RapidStrike;
use Emagia\Randomizer\RandomizerInterface;
use Emagia\Unit\Unit;
use Emagia\Unit\UnitFactory;
use PHPUnit\Framework\TestCase;

class UnitFactoryProvider extends TestCase
{
    /**
     * @var \Emagia\Randomizer\RandomizerInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $randomizer;

    public function setUp(): void
    {
        $this->randomizer = $this->prophesize(RandomizerInterface::class);
    }

    public function valuesForOrderusDataProvider(): array
    {
        return [
            'max values' => [90, 80, 55, 50, 30],
            'min values' => [70, 70, 45, 40, 10],
        ];
    }

    /**
     * @dataProvider valuesForOrderusDataProvider
     *
     * @param int $hp
     * @param int $str
     * @param int $def
     * @param int $speed
     * @param int $luck
     *
     * @throws \ReflectionException
     */
    public function testCreatesOredursWithRapidStrikeAndMagicShield(
        int $hp,
        int $str,
        int $def,
        int $speed,
        int $luck
    ): void
    {
        $this->randomizer->randomize(70, 90)->willReturn($hp);
        $this->randomizer->randomize(70, 80)->willReturn($str);
        $this->randomizer->randomize(45, 55)->willReturn($def);
        $this->randomizer->randomize(40, 50)->willReturn($speed);
        $this->randomizer->randomize(10, 30)->willReturn($luck);

        $factory = new UnitFactory($this->randomizer->reveal());
        $unit = $factory->createOrderus();

        $reflector = new \ReflectionClass($unit);
        $reflectorUnitField = $reflector->getProperty('unit');
        $reflectorUnitField->setAccessible(true);
        $unitWithinMagicShield = $reflectorUnitField->getValue($unit);

        $reflector = new \ReflectionClass($unitWithinMagicShield);
        $reflectorUnitField = $reflector->getProperty('unit');
        $reflectorUnitField->setAccessible(true);
        $unitWithinRapidStrike = $reflectorUnitField->getValue($unitWithinMagicShield);

        $this->assertInstanceOf(MagicShield::class, $unit);
        $this->assertInstanceOf(RapidStrike::class, $unitWithinMagicShield);
        $this->assertInstanceOf(Unit::class, $unitWithinRapidStrike);
    }

    public function valuesForWildBeastDataProvider(): array
    {
        return [
            'max values' => [90, 90, 60, 60, 40],
            'min values' => [60, 60, 40, 40, 25],
        ];
    }

    /**
     * @dataProvider valuesForWildBeastDataProvider
     *
     * @param int $hp
     * @param int $str
     * @param int $def
     * @param int $speed
     * @param int $luck
     */
    public function testCreatesWildBeast(
        int $hp,
        int $str,
        int $def,
        int $speed,
        int $luck
    ): void
    {
        $this->randomizer->randomize(60, 90)->willReturn($hp);
        $this->randomizer->randomize(60, 90)->willReturn($str);
        $this->randomizer->randomize(40, 60)->willReturn($def);
        $this->randomizer->randomize(40, 60)->willReturn($speed);
        $this->randomizer->randomize(25, 40)->willReturn($luck);

        $factory = new UnitFactory($this->randomizer->reveal());
        $unit = $factory->createWildBeast();

        $this->assertInstanceOf(Unit::class, $unit);
    }
}
