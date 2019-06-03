<?php
declare(strict_types=1);

namespace Test\Emagia\Property;

use Emagia\Property\Strength;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class StrengthTest extends TestCase
{
    public function validStrengthPointsProvider(): array
    {
        return [
            'with top-most posiible value' => [90],
            'with lowest possible value' => [60],
            'in beetween value' => [85],
        ];
    }

    public function invalidStrengthPointsProvider(): array
    {
        return [
            'with too big value' => [91],
            'with too small value' => [59],
        ];
    }

    /**
     * @dataProvider validStrengthPointsProvider
     *
     * @doesNotPerformAssertions
     */
    public function testCreatesStrengthPoints(int $value): void
    {
        new Strength($value);
    }

    /**
     * @dataProvider invalidStrengthPointsProvider
     *
     * @param int $value
     */
    public function testThrowsExceptionWhenTriesToCreateStrengthPointsWithInvalidValues(int $value): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Strength($value);
    }

}