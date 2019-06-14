<?php
declare(strict_types=1);

namespace Emagia\Modifier;

use Emagia\Event\RapidStrikeUsedEvent;
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
 * Modifies attack ability
 */
class RapidStrike implements UnitInterface, SubjectInterface
{
    /** @var UnitInterface|SubjectInterface */
    private $unit;
    /** @var RandomizerInterface */
    private $randomizer;
    /** @var int as percentage */
    private const RAPID_STRIKE_CHANCE = 10;

    public function __construct(UnitInterface $unit, RandomizerInterface $randomizer)
    {
        Assert::isInstanceOf($unit, SubjectInterface::class);
        $this->unit = $unit;
        $this->randomizer = $randomizer;
    }

    public function performAttack(UnitInterface $unitToAttack): void
    {
        $chance = $this->randomizer->randomize(1, 100);

        $this->unit->performAttack($unitToAttack);

        // 10% chance
        if ($chance <= self::RAPID_STRIKE_CHANCE && $unitToAttack->isAlive()) {
            $this->unit->notifyObservers(new RapidStrikeUsedEvent($this->unit->getName()));
            $this->unit->performAttack($unitToAttack);
        }
    }

    public function defendFromAttack(Strength $attackStrength): void
    {
        $this->unit->defendFromAttack($attackStrength);
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
