<?php
declare(strict_types=1);

namespace Emagia\Event;

use Emagia\ObserverPattern\Event;
use Emagia\Property\HealthPoints;

class BlockedDamageEvent implements Event
{
    /** @var HealthPoints */
    private $blocked;
    /** @var string */
    private $defenderName;

    public function __construct(string $defenderName, int $blockedDamage)
    {
        $this->defenderName = $defenderName;
        $this->blocked = new HealthPoints($blockedDamage);
    }

    public function getDamage(): HealthPoints
    {
        return $this->blocked;
    }

    public function getDefenderName(): string
    {
        return $this->defenderName;
    }
}
