<?php
declare(strict_types=1);

namespace Emagia\Property;

use Webmozart\Assert\Assert;

class Speed
{
    private $speed;

    public function __construct(int $speed)
    {
        Assert::greaterThanEq($speed, 40);
        Assert::lessThanEq($speed, 60);

        $this->speed = $speed;
    }
}
