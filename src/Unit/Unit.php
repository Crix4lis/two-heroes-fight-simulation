<?php
declare(strict_types=1);

namespace Emagia\Unit;

use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Luck;
use Emagia\Property\Speed;
use Emagia\Property\Strength;

class Unit
{
    private $healthPoints;
    private $strength;
    private $defence;
    private $speed;
    private $luck;

    public function __construct(
        HealthPoints $healthPoints,
        Strength $strength,
        Defence $defence,
        Speed $speed,
        Luck $luck
    ){
        $this->healthPoints = $healthPoints;
        $this->strength = $strength;
        $this->defence = $defence;
        $this->speed = $speed;
        $this->luck = $luck;
    }

    public function performAttack(Unit $unitToAttack): void
    {
        //todo: event zaatakowano
        $unitToAttack->defendFrom($this);
    }

    public function defendFrom(Unit $defendFrom): void
    {
        $blocked = $this->defence->getPoints();
        //todo: event zablokowano $blocked;
        $dmgToReceive = $defendFrom->getAttackStrength()->getPoints() - $blocked;

        if ($dmgToReceive < 0) {
            $dmgToReceive = 0;
        }

        $this->receiveDamage(new HealthPoints($dmgToReceive));
    }

    public function getAttackStrength(): Strength
    {
        return $this->strength;
    }

    public function getCurrentHealth(): HealthPoints
    {
        return $this->healthPoints;
    }

    private function receiveDamage(HealthPoints $receiveDamage): void
    {
        //todo: event otrzymano
        $this->healthPoints = $this->healthPoints->subtract($receiveDamage);
    }
}
