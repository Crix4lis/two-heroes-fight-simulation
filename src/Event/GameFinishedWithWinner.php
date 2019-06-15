<?php
declare(strict_types=1);

namespace Emagia\Event;

use Emagia\ObserverPattern\Event;
use Emagia\Property\HealthPoints;

class GameFinishedWithWinner implements Event
{
    /**
     * @var string
     */
    private $winnerName;
    /**
     * @var \Emagia\Property\HealthPoints
     */
    private $winnerHp;
    /**
     * @var int
     */
    private $turn;

    public function __construct(string $winnerName, HealthPoints $winnerHp, int $turn)
    {
        $this->winnerName = $winnerName;
        $this->winnerHp = $winnerHp;
        $this->turn = $turn;
    }

    /**
     * @return string
     */
    public function getWinnerName(): string
    {
        return $this->winnerName;
    }

    /**
     * @return \Emagia\Property\HealthPoints
     */
    public function getWinnerHp(): \Emagia\Property\HealthPoints
    {
        return $this->winnerHp;
    }

    /**
     * @return int
     */
    public function getTurn(): int
    {
        return $this->turn;
    }
}
