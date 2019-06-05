<?php
declare(strict_types=1);

namespace Emagia\Property;

use Webmozart\Assert\Assert;

class Speed implements ComparisionInterface, PropertyPointsInterface
{
    private $speed;

    public function __construct(int $speed)
    {
        Assert::greaterThanEq($speed, 40);
        Assert::lessThanEq($speed, 60);

        $this->speed = $speed;
    }

    public function isGreater(PropertyPointsInterface $property): bool
    {
        Assert::isInstanceOf($property, __class__);

        return $this->speed > $property->getPoints();
    }

    public function getPoints(): int
    {
        return $this->speed;
    }

    public function isEqual(PropertyPointsInterface $property): bool
    {
        Assert::isInstanceOf($property, __class__);

        return $this->speed === $property->getPoints();
    }
}
