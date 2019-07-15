<?php
declare(strict_types=1);

namespace Emagia\Property;

use Webmozart\Assert\Assert;

class HealthPoints implements PropertyPointsInterface
{
    private $health;

    public function __construct(int $health)
    {
        Assert::greaterThanEq($health, 0);
        Assert::lessThanEq($health, 100);

        $this->health = $health;
    }

    public function subtract(HealthPoints $toSubtract): HealthPoints
    {
        $rest = $this->health - $toSubtract->getPoints();

        if ($rest < 0) {
            return new HealthPoints(0);
        }

        return new HealthPoints($rest);
    }

    public function getPoints(): int
    {
        return $this->health;
    }

    public function reduceTimes(int $times): HealthPoints
    {
        Assert::greaterThan($times, 0);
        $result = $this->health / $times;

        return new HealthPoints((int) round($result, 0));
    }

    public function __toString()
    {
        return (string)$this->health;
    }
}
