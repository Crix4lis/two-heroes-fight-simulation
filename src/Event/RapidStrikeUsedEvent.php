<?php
declare(strict_types=1);

namespace Emagia\Event;

use Emagia\ObserverPattern\Event;

class RapidStrikeUsedEvent implements Event
{
    /** @var string */
    private $attackerName;

    public function __construct(string $attackerName)
    {
        $this->attackerName = $attackerName;
    }

    public function getAttackerName(): string
    {
        return $this->attackerName;
    }
}
