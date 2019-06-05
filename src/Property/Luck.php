<?php
declare(strict_types=1);

namespace Emagia\Property;

use Webmozart\Assert\Assert;

class Luck implements ComparisionInterface, PropertyPointsInterface
{
    private $luck;

    /**
     * @param int $luck percentage of luck as int (20% = 20)
     */
    public function __construct(int $luck)
    {
        Assert::greaterThanEq($luck, 10);
        Assert::lessThanEq($luck, 45);

        $this->luck = $luck;
    }

    public function isGreater(PropertyPointsInterface $property): bool
    {
        Assert::isInstanceOf($property, __class__);

        return $this->luck > $property->getPoints();
    }

    public function getPoints(): int
    {
        return $this->luck;
    }

    public function isEqual(PropertyPointsInterface $property): bool
    {
        Assert::isInstanceOf($property, __class__);

        return $this->luck === $property->getPoints();
    }
}
