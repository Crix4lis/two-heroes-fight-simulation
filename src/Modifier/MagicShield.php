<?php
declare(strict_types=1);

namespace Emagia\Modifier;

use Emagia\Event\BlockedDamageEvent;
use Emagia\Event\MagicShieldUsedEvent;
use Emagia\ObserverPattern\Event;
use Emagia\ObserverPattern\ObserverInterface;
use Emagia\ObserverPattern\SubjectInterface;
use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Luck;
use Emagia\Property\Speed;
use Emagia\Property\Strength;
use Emagia\Randomizer\RandomizerInterface;
use Emagia\Unit\IdentityInterface;
use Emagia\Unit\Unit;
use Emagia\Unit\UnitInterface;
use Webmozart\Assert\Assert;

/**
 * Decorator for:
 * @see Unit
 * Modifies defense ability
 */
class MagicShield implements UnitInterface, SubjectInterface
{
    /** @var UnitInterface|SubjectInterface */
    private $unit;
    /** @var RandomizerInterface */
    private $randomizer;
    /** @var int means % */
    private const MAGIC_SHIELD_CHANCE = 20;

    public function __construct(UnitInterface $unit, RandomizerInterface $randomizer)
    {
        Assert::isInstanceOf($unit, SubjectInterface::class);
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
        $this->unit->notifyObservers(new BlockedDamageEvent($this->unit->getName(), $blocked));
        $ptsToReceive = $attackStrength->getPoints() - $blocked;
        $dmgToReceive = $this->useMagicShield(new HealthPoints($ptsToReceive));
        $this->receiveDamage($dmgToReceive);
    }

    private function useMagicShield(HealthPoints $dmgToReceive): HealthPoints
    {
        $chance = $this->randomizer->randomize(1, 100);

        if ($chance <= self::MAGIC_SHIELD_CHANCE) {
            $dmgToReceive = $dmgToReceive->reduceTimes(2);
            $this->unit->notifyObservers(new MagicShieldUsedEvent($this->unit->getName(), $dmgToReceive->getPoints()));
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

    public function isAlive(): bool
    {
        return $this->unit->isAlive();
    }

    public function getLuck(): Luck
    {
        return $this->unit->getLuck();
    }

    public function getSpeed(): Speed
    {
        return $this->unit->getSpeed();
    }

    public function isTheSameInstance(IdentityInterface $instance): bool
    {
        return $this->unit->isTheSameInstance($instance);
    }

    public function getIdentity(): string
    {
        return $this->unit->getIdentity();
    }

    public function getName(): string
    {
        return $this->unit->getName();
    }

    public function register(ObserverInterface $observer): void
    {
        $this->unit->register($observer);
    }

    public function notifyObservers(Event $event): void
    {
        $this->unit->notifyObservers($event);
    }
}
