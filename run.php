<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';

//initialize "container"
$randomizer = new Emagia\Randomizer\DumbRandomizer();
$turn = new \Emagia\TurnService();
$unitFactory = new \Emagia\Unit\UnitFactory($randomizer);
$resolver = new \Emagia\AttackerResolver();
$gamePlay = new \Emagia\GamePlayService($turn, $unitFactory, $resolver);

$gamePlay->startBattle();
