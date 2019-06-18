<?php
declare(strict_types=1);

namespace Emagia\Event;

use Emagia\MediatorPattern\Event;
use Emagia\Unit\UnitInterface;

class DefenderAlredyDeadEvent implements Event
{
    /** @var UnitInterface */
    private $alreadyDead;
    /** @var UnitInterface */
    private $attacker;

    public function __construct(UnitInterface $attacker, UnitInterface $deadUnit)
    {
        $this->alreadyDead = $deadUnit;
        $this->attacker = $attacker;
    }

    public function getDead(): UnitInterface
    {
        return $this->alreadyDead;
    }

    public function getAttacker(): UnitInterface
    {
        return $this->attacker;
    }
}
