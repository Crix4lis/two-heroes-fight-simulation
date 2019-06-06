<?php
declare(strict_types=1);

namespace Emagia\Unit;

use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Luck;
use Emagia\Property\Speed;
use Emagia\Property\Strength;

class Unit implements UnitInterface
{
    private $healthPoints;
    private $strength;
    private $defence;
    private $speed;
    private $luck;
    private $identity;

    public function __construct(
        HealthPoints $healthPoints,
        Strength $strength,
        Defence $defence,
        Speed $speed,
        Luck $luck
    ){
        $hash = $healthPoints->getPoints() . $strength->getPoints()
            . $defence->getPoints() . $speed->getPoints() . $luck->getPoints();

        $this->healthPoints = $healthPoints;
        $this->strength = $strength;
        $this->defence = $defence;
        $this->speed = $speed;
        $this->luck = $luck;
        $this->identity = md5((new \DateTime('now'))->format('Y-m-d H:i:s') . $hash); //dumb identity
    }

    public function performAttack(UnitInterface $unitToAttack): void
    {
        if ($unitToAttack->getCurrentHealth()->getPoints() <= 0) {
            //todo: already dead - event?
            return;
        }

        //todo: event zaatakowano
        $unitToAttack->defendFromAttack($this->strength);
    }

    public function defendFromAttack(Strength $attackStrength): void
    {
        $blocked = $this->defence->getPoints();
        //todo: event zablokowano $blocked;
        $dmgToReceive = $attackStrength->getPoints() - $blocked;

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

    public function receiveDamage(HealthPoints $receiveDamage): void
    {
        //todo: event otrzymano
        $this->healthPoints = $this->healthPoints->subtract($receiveDamage);
    }

    public function getDefense(): Defence
    {
        return $this->defence;
    }

    public function isAlive(): bool
    {
        return $this->healthPoints->getPoints() > 0;
    }

    public function getLuck(): Luck
    {
        return $this->luck;
    }

    public function getSpeed(): Speed
    {
        return $this->speed;
    }

    public function isTheSameInstance(IdentityInterface $instance): bool
    {
        return $this->identity === $instance->getIdentity();
    }

    public function getIdentity(): string
    {
        return $this->identity;
    }
}
