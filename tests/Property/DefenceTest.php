<?php
declare(strict_types=1);

namespace Test\Emagia\Property;

use Emagia\Property\Defence;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DefenceTest extends TestCase
{
    public function validDefencePointsProvider(): array
    {
        return [
            'with top-most posiible value' => [60],
            'with lowest possible value' => [40],
            'in beetween value' => [50],
        ];
    }

    public function invalidDefencePointsProvider(): array
    {
        return [
            'with too big value' => [61],
            'with too small value' => [39],
        ];
    }

    /**
     * @dataProvider validDefencePointsProvider
     *
     * @doesNotPerformAssertions
     */
    public function testCreatesDefencePoints(int $value): void
    {
        new Defence($value);
    }

    /**
     * @dataProvider invalidDefencePointsProvider
     *
     * @param int $value
     */
    public function testThrowsExceptionWhenTriesToCreateDefencePointsWithInvalidValues(int $value): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Defence($value);
    }
}
