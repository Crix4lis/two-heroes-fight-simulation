<?php
declare(strict_types=1);

namespace Emagia\Property;

use Webmozart\Assert\Assert;

class Luck
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
}
