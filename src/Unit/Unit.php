<?php
declare(strict_types=1);

namespace Emagia\Unit;

use Emagia\Event\BlockedDamageEvent;
use Emagia\Event\DefenderAlredyDeadEvent;
use Emagia\Event\PerformedAttackEvent;
use Emagia\Event\ReceivedDamageEvent;
use Emagia\ObserverPattern\SubjectInterface;
use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Luck;
use Emagia\Property\Speed;
use Emagia\Property\Strength;
use Emagia\Subject;

final class Unit implements UnitInterface, SubjectInterface
{
    use Subject;
    /** @var string */
    private $name;
    private $healthPoints;
    private $strength;
    private $defence;
    private $speed;
    private $luck;
    private $identity;

    public function __construct(
        string $name,
        HealthPoints $healthPoints,
        Strength $strength,
        Defence $defence,
        Speed $speed,
        Luck $luck
    ){
        $hash = $healthPoints->getPoints() . $strength->getPoints()
            . $defence->getPoints() . $speed->getPoints() . $luck->getPoints();

        $this->name = $name;
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
            $this->notifyObservers(new DefenderAlredyDeadEvent($this, $unitToAttack));
            return;
        }

        $this->notifyObservers(new PerformedAttackEvent($this->name, $this->strength->getPoints()));
        $unitToAttack->defendFromAttack($this->strength);
    }

    public function defendFromAttack(Strength $attackStrength): void
    {
        $blocked = $this->defence->getPoints();
        $dmgToReceive = $attackStrength->getPoints() - $blocked;
        $this->notifyObservers(new BlockedDamageEvent($this->name, $blocked));

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
        $this->notifyObservers(new ReceivedDamageEvent($this->name, $receiveDamage->getPoints()));
        //todo: event dead
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

    public function getName(): string
    {
        return $this->name;
    }
}
