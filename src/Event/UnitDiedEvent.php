<?php
declare(strict_types=1);

namespace Emagia\Event;

use Emagia\ObserverPattern\Event;

class UnitDiedEvent implements Event
{
    /** @var string */
    private $unitName;

    public function __construct(string $unitName)
    {
        $this->unitName = $unitName;
    }

    public function getUnitName(): string
    {
        return $this->unitName;
    }
}
