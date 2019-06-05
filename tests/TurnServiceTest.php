<?php
declare(strict_types=1);

namespace Test\Emagia;

use Emagia\TurnService;
use Emagia\Unit\UnitInterface;
use LogicException;
use PHPUnit\Framework\TestCase;

class TurnServiceTest extends TestCase
{
    /**
     * @var \Emagia\Unit\UnitInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $attacker;
    /**
     * @var \Emagia\Unit\UnitInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $defender;

    public function setUp(): void
    {
        $this->attacker = $this->prophesize(UnitInterface::class);
        $this->defender = $this->prophesize(UnitInterface::class);
    }

    public function testAttacksDefender(): void
    {
        $this->attacker->isAlive()->willReturn(true);
        $this->defender->isAlive()->willReturn(true);
        $this->attacker->performAttack($this->defender->reveal())->shouldBeCalled(); //shouldBeCalled is an assertion

        $turn = new TurnService();
        $turn->make($this->attacker->reveal(), $this->defender->reveal());
    }

    public function testAttacksDeadDefender(): void
    {
        $this->attacker->isAlive()->willReturn(true);
        $this->defender->isAlive()->willReturn(false);
        $this->attacker->performAttack($this->defender->reveal())->shouldNotBeCalled(); //shouldBeCalled is an assertion

        $turn = new TurnService();
        $turn->make($this->attacker->reveal(), $this->defender->reveal());
    }

    public function testTriesToAttackWithDeadAttacker(): void
    {
        $this->expectException(LogicException::class);
        $this->attacker->isAlive()->willReturn(false);
        $this->defender->isAlive()->willReturn(true);
        $this->attacker->performAttack($this->defender->reveal())->shouldNotBeCalled(); //shouldBeCalled is an assertion

        $turn = new TurnService();
        $turn->make($this->attacker->reveal(), $this->defender->reveal());
    }
}
