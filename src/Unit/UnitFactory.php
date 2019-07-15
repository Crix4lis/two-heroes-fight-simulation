<?php
declare(strict_types=1);

namespace Emagia\Unit;

use Emagia\MediatorPattern\ColleagueInterface;
use Emagia\Modifier\MagicShield;
use Emagia\Modifier\RapidStrike;
use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Luck;
use Emagia\Property\Speed;
use Emagia\Property\Strength;
use Emagia\Randomizer\RandomizerInterface;

class UnitFactory
{
    /** @var RandomizerInterface */
    private $randomizer;

    public function __construct(RandomizerInterface $randomizer)
    {
        $this->randomizer = $randomizer;
    }

    /**
     * @return UnitInterface|ColleagueInterface
     */
    public function createWildBeast(): UnitInterface
    {
        $hp = new HealthPoints($this->randomizer->randomize(60, 90));
        $strength = new Strength($this->randomizer->randomize(60, 90));
        $defence = new Defence($this->randomizer->randomize(40, 60));
        $speed = new Speed($this->randomizer->randomize(40, 60));
        $luck = new Luck($this->randomizer->randomize(25, 40));

        return new Unit('Wild Beast', $hp, $strength, $defence, $speed, $luck);
    }

    /**
     * @return UnitInterface|ColleagueInterface
     */
    public function createOrderus(): UnitInterface
    {
        $hp = new HealthPoints($this->randomizer->randomize(70, 90));
        $strength = new Strength($this->randomizer->randomize(70, 80));
        $defence = new Defence($this->randomizer->randomize(45, 55));
        $speed = new Speed($this->randomizer->randomize(40, 50));
        $luck = new Luck($this->randomizer->randomize(10, 30));

        return new MagicShield(
            new RapidStrike(
                new Unit('Orderus', $hp, $strength, $defence, $speed, $luck),
                $this->randomizer
            ),
            $this->randomizer
        );
    }
}
