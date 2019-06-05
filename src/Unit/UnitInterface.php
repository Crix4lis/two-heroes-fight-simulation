<?php
declare(strict_types=1);

namespace Emagia\Unit;

use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Strength;

interface UnitInterface
{
    public function performAttack(UnitInterface $unitToAttack): void;
    public function defendFromAttack(Strength $attackStrength): void;
    public function getAttackStrength(): Strength;
    public function getCurrentHealth(): HealthPoints;
    public function receiveDamage(HealthPoints $receiveDamage): void;
    public function getDefense(): Defence;
}
