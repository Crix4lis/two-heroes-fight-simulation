<?php
declare(strict_types=1);

namespace Emagia\Property;

use Webmozart\Assert\Assert;

class Defence implements PropertyPointsInterface
{
    private $defence;

    public function __construct(int $defence)
    {
        Assert::greaterThanEq($defence, 40);
        Assert::lessThanEq($defence, 60);

        $this->defence = $defence;
    }

    public function getPoints(): int
    {
        return $this->defence;
    }

    public function __toString()
    {
        return (string)$this->defence;
    }
}
