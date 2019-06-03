<?php
declare(strict_types=1);

namespace Test\Emagia\Property;

use Emagia\Property\HealthPoints;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class HealthPointsTest extends TestCase
{
    public function validPointsProvider(): array
    {
        return [
            'biggest possible value' => [100],
            'smallest possible value' => [0],
            'in between value' => [50],
        ];
    }

    public function invalidPointsDataProvider(): array
    {
        return [
            'too small value' => [-1],
            'too big value' => [101],
        ];
    }

    public function subtractPointsProvider(): array
    {
        return [
            'start: 100; minus: 80' => [100, 80, 20],
            'start: 100; minus: 100' => [100, 100, 0],
            'start: 90; minus: 91' => [90, 91, 0],
            'start: 1; minus: 91' => [1, 91, 0],
            'start: 100; minus: 0' => [100, 0, 100],
        ];
    }

    /**
     * @dataProvider validPointsProvider
     *
     * @doesNotPerformAssertions
     *
     * @param int $value
     */
    public function testCreatesHealthPointsWithValidValues(int $value): void
    {
        new HealthPoints($value);
    }

    /**
     * @dataProvider invalidPointsDataProvider
     * @param int $value
     */
    public function testThrowsExceptionWhenTriesToCreateHealthPointsWithInvalidValues(int $value): void
    {
        $this->expectException(InvalidArgumentException::class);

        new HealthPoints($value);
    }

    /**
     * @dataProvider subtractPointsProvider
     *
     * @param int $initial
     * @param int $toSubtract
     * @param int $expected
     */
    public function testSubtractsHealthPoints(int $initial, int $toSubtract, int $expected): void
    {
        $hp = new HealthPoints($initial);
        $toSubtract = new HealthPoints($toSubtract);
        $subtracted = $hp->subtract($toSubtract);

        $this->assertEquals($expected, $subtracted->getPoints());
    }
}
