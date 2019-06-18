<?php
declare(strict_types=1);

namespace Emagia\Event;

use Emagia\MediatorPattern\Event;
use Emagia\Property\HealthPoints;

class PerformedAttackEvent implements Event
{
    /** @var string */
    private $attackerName;
    /** @var HealthPoints */
    private $attackedWithDamage;

    public function __construct(string $attackerName, int $attackDamage)
    {
        $this->attackerName = $attackerName;
        $this->attackedWithDamage = new HealthPoints($attackDamage);
    }

    public function getAttackerName(): string
    {
        return $this->attackerName;
    }

    public function getAttackedWithDamage(): HealthPoints
    {
        return $this->attackedWithDamage;
    }
}
