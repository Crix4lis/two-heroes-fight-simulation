<?php
declare(strict_types=1);

namespace Emagia\Property;

use Webmozart\Assert\Assert;

class Strength implements PropertyPointsInterface
{
    private $strength;

    public function __construct(int $strength)
    {
        Assert::greaterThanEq($strength, 60);
        Assert::lessThanEq($strength, 90);

        $this->strength = $strength;
    }

    public function getPoints(): int
    {
        return $this->strength;
    }

    public function __toString()
    {
        return (string)$this->strength;
    }
}
