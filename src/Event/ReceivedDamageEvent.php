<?php
declare(strict_types=1);

namespace Emagia\Event;

use Emagia\MediatorPattern\Event;
use Emagia\Property\HealthPoints;

class ReceivedDamageEvent implements Event
{
    /** @var HealthPoints */
    private $receivedDamage;
    /** @var string */
    private $defenderName;
    /** @var HealthPoints */
    private $defenderHpLeft;

    public function __construct(string $defenderName, int $receivedDamage, int $defenderHpLeft)
    {
        $this->receivedDamage = new HealthPoints($receivedDamage);
        $this->defenderName = $defenderName;
        $this->defenderHpLeft = new HealthPoints($defenderHpLeft);
    }

    public function getReceivedDamage(): HealthPoints
    {
        return $this->receivedDamage;
    }

    public function getDefenderName(): string
    {
        return $this->defenderName;
    }

    public function getDefenderHpLeft(): HealthPoints
    {
        return $this->defenderHpLeft;
    }
}
