<?php
declare(strict_types=1);

namespace Emagia\Modifier;

use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Strength;
use Emagia\Randomizer\RandomizerInterface;
use Emagia\Unit\Unit;
use Emagia\Unit\UnitInterface;

/**
 * Decorator for:
 * @see Unit
 * Modifies defense ability
 */
class MagicShield implements UnitInterface
{
    /** @var UnitInterface */
    private $unit;
    /** @var RandomizerInterface */
    private $randomizer;
    /** @var int means % */
    private const MAGIC_SHIELD_CHANCE = 20;

    public function __construct(UnitInterface $unit, RandomizerInterface $randomizer)
    {
        $this->unit = $unit;
        $this->randomizer = $randomizer;
    }

    public function performAttack(UnitInterface $unitToAttack): void
    {
        $this->unit->performAttack($unitToAttack);
    }

    public function defendFromAttack(Strength $attackStrength): void
    {
        $blocked = $this->unit->getDefense()->getPoints();
        //todo: event zablokowano $blocked;
        $dmgToReceive = $attackStrength->getPoints() - $blocked;

        if ($dmgToReceive < 0) {
            $dmgToReceive = 0;
        }

        $dmgToReceive = $this->useMagicShield(new HealthPoints($dmgToReceive));
        $this->receiveDamage($dmgToReceive);
    }

    private function useMagicShield(HealthPoints $dmgToReceive): HealthPoints
    {
        $chance = $this->randomizer->randomize(1, 100);

        if ($chance <= self::MAGIC_SHIELD_CHANCE) {
            $dmgToReceive = $dmgToReceive->reduceTimes(2);
            //todo: event magic shield used and reduced dmg
        }

        return $dmgToReceive;
    }

    public function getAttackStrength(): Strength
    {
        return $this->unit->getAttackStrength();
    }

    public function getCurrentHealth(): HealthPoints
    {
        return $this->unit->getCurrentHealth();
    }

    public function receiveDamage(HealthPoints $receiveDamage): void
    {
        $this->unit->receiveDamage($receiveDamage);
    }

    public function getDefense(): Defence
    {
        return $this->unit->getDefense();
    }
}
