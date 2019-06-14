<?php
declare(strict_types=1);

namespace Emagia\Event;

use Emagia\ObserverPattern\Event;
use Emagia\Property\HealthPoints;

class ReceivedDamageEvent implements Event
{
    /** @var HealthPoints */
    private $receivedDamage;
    /** @var string */
    private $defenderName;

    public function __construct(string $defenderName, int $receivedDamage)
    {
        $this->receivedDamage = new HealthPoints($receivedDamage);
        $this->defenderName = $defenderName;
    }

    public function getReceivedDamage(): HealthPoints
    {
        return $this->receivedDamage;
    }

    public function getDefenderName(): string
    {
        return $this->defenderName;
    }
}
