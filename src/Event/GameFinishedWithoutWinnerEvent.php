<?php
declare(strict_types=1);

namespace Emagia\Event;

use Emagia\MediatorPattern\Event;
use Emagia\Property\HealthPoints;

class GameFinishedWithoutWinnerEvent implements Event
{
    /**
     * @var string
     */
    private $firstUnitName;
    /**
     * @var \Emagia\Property\HealthPoints
     */
    private $firstUnitHp;
    /**
     * @var string
     */
    private $secondUnitName;
    /**
     * @var \Emagia\Property\HealthPoints
     */
    private $secondUnitHp;
    /**
     * @var int
     */
    private $turn;

    /**
     * GameFinishedWithoutWinnerEvent constructor.
     */
    public function __construct(
        string $firstUnitName,
        HealthPoints $firstUnitHp,
        string $secondUnitName,
        HealthPoints $secondUnitHp,
        int $turn
    ) {
        $this->firstUnitName = $firstUnitName;
        $this->firstUnitHp = $firstUnitHp;
        $this->secondUnitName = $secondUnitName;
        $this->secondUnitHp = $secondUnitHp;
        $this->turn = $turn;
    }

    /**
     * @return string
     */
    public function getFirstUnitName(): string
    {
        return $this->firstUnitName;
    }

    /**
     * @return \Emagia\Property\HealthPoints
     */
    public function getFirstUnitHp(): \Emagia\Property\HealthPoints
    {
        return $this->firstUnitHp;
    }

    /**
     * @return string
     */
    public function getSecondUnitName(): string
    {
        return $this->secondUnitName;
    }

    /**
     * @return \Emagia\Property\HealthPoints
     */
    public function getSecondUnitHp(): \Emagia\Property\HealthPoints
    {
        return $this->secondUnitHp;
    }

    /**
     * @return int
     */
    public function getTurn(): int
    {
        return $this->turn;
    }
}
