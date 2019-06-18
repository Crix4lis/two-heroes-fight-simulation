<?php
declare(strict_types=1);

namespace Emagia\Event;

use Emagia\ObserverPattern\Event;
use Emagia\Property\Defence;
use Emagia\Property\HealthPoints;
use Emagia\Property\Luck;
use Emagia\Property\Speed;
use Emagia\Property\Strength;

class GameStartedEvent implements Event
{
    /**
     * @var string
     */
    private $attackerName;
    /**
     * @var \Emagia\Property\HealthPoints
     */
    private $attackerHealth;
    /**
     * @var \Emagia\Property\Strength
     */
    private $attackerStrength;
    /**
     * @var \Emagia\Property\Defence
     */
    private $attackerDefense;
    /**
     * @var \Emagia\Property\Speed
     */
    private $attackerSpeed;
    /**
     * @var \Emagia\Property\Luck
     */
    private $attackerLuck;
    /**
     * @var string
     */
    private $defenderName;
    /**
     * @var \Emagia\Property\HealthPoints
     */
    private $defenderHealth;
    /**
     * @var \Emagia\Property\Strength
     */
    private $defenderStrength;
    /**
     * @var \Emagia\Property\Defence
     */
    private $defenderDefense;
    /**
     * @var \Emagia\Property\Speed
     */
    private $defenderSpeed;
    /**
     * @var \Emagia\Property\Luck
     */
    private $defenderLuck;
    /**
     * @var int
     */
    private $maxRounds;

    public function __construct(
        string $attackerName,
        HealthPoints $attackerHealth,
        Strength $attackerStrength,
        Defence $attackerDefense,
        Speed $attackerSpeed,
        Luck $attackerLuck,
        string $defenderName,
        HealthPoints $defenderHealth,
        Strength $defenderStrength,
        Defence $defenderDefense,
        Speed $defenderSpeed,
        Luck $defenderLuck,
        int $maxRounds
    )
    {
        $this->attackerName = $attackerName;
        $this->attackerHealth = $attackerHealth;
        $this->attackerStrength = $attackerStrength;
        $this->attackerDefense = $attackerDefense;
        $this->attackerSpeed = $attackerSpeed;
        $this->attackerLuck = $attackerLuck;
        $this->defenderName = $defenderName;
        $this->defenderHealth = $defenderHealth;
        $this->defenderStrength = $defenderStrength;
        $this->defenderDefense = $defenderDefense;
        $this->defenderSpeed = $defenderSpeed;
        $this->defenderLuck = $defenderLuck;
        $this->maxRounds = $maxRounds;
    }

    /**
     * @return string
     */
    public function getAttackerName(): string
    {
        return $this->attackerName;
    }

    /**
     * @return \Emagia\Property\HealthPoints
     */
    public function getAttackerHealth(): \Emagia\Property\HealthPoints
    {
        return $this->attackerHealth;
    }

    /**
     * @return \Emagia\Property\Strength
     */
    public function getAttackerStrength(): \Emagia\Property\Strength
    {
        return $this->attackerStrength;
    }

    /**
     * @return \Emagia\Property\Defence
     */
    public function getAttackerDefense(): \Emagia\Property\Defence
    {
        return $this->attackerDefense;
    }

    /**
     * @return \Emagia\Property\Speed
     */
    public function getAttackerSpeed(): \Emagia\Property\Speed
    {
        return $this->attackerSpeed;
    }

    /**
     * @return \Emagia\Property\Luck
     */
    public function getAttackerLuck(): \Emagia\Property\Luck
    {
        return $this->attackerLuck;
    }

    /**
     * @return string
     */
    public function getDefenderName(): string
    {
        return $this->defenderName;
    }

    /**
     * @return \Emagia\Property\HealthPoints
     */
    public function getDefenderHealth(): \Emagia\Property\HealthPoints
    {
        return $this->defenderHealth;
    }

    /**
     * @return \Emagia\Property\Strength
     */
    public function getDefenderStrength(): \Emagia\Property\Strength
    {
        return $this->defenderStrength;
    }

    /**
     * @return \Emagia\Property\Defence
     */
    public function getDefenderDefense(): \Emagia\Property\Defence
    {
        return $this->defenderDefense;
    }

    /**
     * @return \Emagia\Property\Speed
     */
    public function getDefenderSpeed(): \Emagia\Property\Speed
    {
        return $this->defenderSpeed;
    }

    /**
     * @return \Emagia\Property\Luck
     */
    public function getDefenderLuck(): \Emagia\Property\Luck
    {
        return $this->defenderLuck;
    }

    /**
     * @return int
     */
    public function getMaxRounds(): int
    {
        return $this->maxRounds;
    }
}
