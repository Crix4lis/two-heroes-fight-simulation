<?php
declare(strict_types=1);

namespace Emagia\Unit;

use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Luck;
use Emagia\Property\Speed;
use Emagia\Property\Strength;

interface UnitInterface extends IdentityInterface
{
    public function performAttack(UnitInterface $unitToAttack): void;
    public function defendFromAttack(Strength $attackStrength): void;
    public function getAttackStrength(): Strength;
    public function getCurrentHealth(): HealthPoints;
    public function receiveDamage(HealthPoints $receiveDamage): void;
    public function getDefense(): Defence;
    public function isAlive(): bool;
    public function getLuck(): Luck;
    public function getSpeed(): Speed;
    public function getName(): string;
}
