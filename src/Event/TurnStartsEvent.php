<?php
declare(strict_types=1);

namespace Emagia\Event;

use Emagia\ObserverPattern\Event;

class TurnStartsEvent implements Event
{
    /**
     * @var string
     */
    private $attackerName;
    /**
     * @var string
     */
    private $defenderName;
    /**
     * @var int
     */
    private $turn;

    public function __construct(string $attackerName, string $defenderName, int $turn)
    {
        $this->attackerName = $attackerName;
        $this->defenderName = $defenderName;
        $this->turn = $turn;
    }

    /**
     * @return string
     */
    public function getAttackerName(): string
    {
        return $this->attackerName;
    }

    /**
     * @return string
     */
    public function getDefenderName(): string
    {
        return $this->defenderName;
    }

    /**
     * @return int
     */
    public function getTurn(): int
    {
        return $this->turn;
    }
}
