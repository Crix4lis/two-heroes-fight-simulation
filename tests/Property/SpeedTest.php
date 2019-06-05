<?php
declare(strict_types=1);

namespace Test\Emagia\Property;

use Emagia\Property\Speed;
use PHPUnit\Framework\TestCase;

class SpeedTest extends TestCase
{
    private $speedMock;

    public function setUp(): void
    {
        $this->speedMock = $this->prophesize(Speed::class);
    }

    public function notEqualSpeedDataProvider(): array
    {
        return [
            'true greater' => [50, 40, true],
            'true smaller' => [40, 50, false],
        ];
    }

    /**
     * @dataProvider notEqualSpeedDataProvider
     *
     * @param int  $trueSpeed
     * @param int  $fakeSpeed
     * @param bool $expectedTrueGreater
     */
    public function testComparesNotEqualLucks(int $trueSpeed, int $fakeSpeed, bool $expectedTrueGreater): void
    {
        $this->speedMock->getPoints()->willReturn($fakeSpeed);
        $luck = new Speed($trueSpeed);
        $isEqual = $luck->isEqual($this->speedMock->reveal());
        $isGreater = $luck->isGreater($this->speedMock->reveal());

        $this->assertFalse($isEqual);
        $this->assertEquals($expectedTrueGreater, $isGreater);
    }

    public function testComparesEqualSpeeds(): void
    {
        $this->speedMock->getPoints()->willReturn(40);
        $luck = new Speed(40);
        $isEqual = $luck->isEqual($this->speedMock->reveal());
        $isGreater = $luck->isGreater($this->speedMock->reveal());

        $this->assertTrue($isEqual);
        $this->assertFalse($isGreater);
    }
}
