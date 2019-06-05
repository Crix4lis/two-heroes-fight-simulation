<?php
declare(strict_types=1);

namespace Test\Emagia\Property;

use Emagia\Property\Luck;
use PHPUnit\Framework\TestCase;

class LuckTest extends TestCase
{
    /**
     * @var \Emagia\Property\Luck|\Prophecy\Prophecy\ObjectProphecy
     */
    private $luckMock;

    public function setUp(): void
    {
        $this->luckMock = $this->prophesize(Luck::class);
    }

    public function notEqualLuckDataProvider(): array
    {
        return [
            'true greater' => [20, 10, true],
            'true smaller' => [10, 20, false],
        ];
    }

    /**
     * @dataProvider notEqualLuckDataProvider
     *
     * @param int  $trueLuck
     * @param int  $fakeLuck
     * @param bool $expectedTrueGreater
     */
    public function testComparesNotEqualLucks(int $trueLuck, int $fakeLuck, bool $expectedTrueGreater): void
    {
        $this->luckMock->getPoints()->willReturn($fakeLuck);
        $luck = new Luck($trueLuck);
        $isEqual = $luck->isEqual($this->luckMock->reveal());
        $isGreater = $luck->isGreater($this->luckMock->reveal());

        $this->assertFalse($isEqual);
        $this->assertEquals($expectedTrueGreater, $isGreater);
    }

    public function testComparesEqualLucks(): void
    {
        $this->luckMock->getPoints()->willReturn(10);
        $luck = new Luck(10);
        $isEqual = $luck->isEqual($this->luckMock->reveal());
        $isGreater = $luck->isGreater($this->luckMock->reveal());

        $this->assertTrue($isEqual);
        $this->assertFalse($isGreater);
    }
}
