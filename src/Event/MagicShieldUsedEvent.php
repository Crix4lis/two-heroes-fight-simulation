<?php
declare(strict_types=1);

namespace Emagia\Event;

use Emagia\ObserverPattern\Event;
use Emagia\Property\HealthPoints;

class MagicShieldUsedEvent implements Event
{
    /** @var HealthPoints */
    private $reducedDamageTo;
    /** @var string */
    private $defenderName;

    public function __construct(string $defenderName, int $damageReducedTo)
    {
        $this->defenderName = $defenderName;
        $this->reducedDamageTo = new HealthPoints($damageReducedTo);
    }

    public function getDamageReducedTo(): HealthPoints
    {
        return $this->reducedDamageTo;
    }

    public function getDefenderName(): string
    {
        return $this->defenderName;
    }
}
