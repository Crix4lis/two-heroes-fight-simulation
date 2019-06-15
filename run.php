<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';

//initialize "container"
$randomizer = new Emagia\Randomizer\DumbRandomizer();
$turn = new \Emagia\TurnService();
$loggableTurn = new \Emagia\Logger\TurnServiceLogger(
    $turn,
    new \Monolog\Logger('Emagia turn logger'),
    \Monolog\Logger::DEBUG
);
$unitFactory = new \Emagia\Unit\UnitFactory($randomizer);
$resolver = new \Emagia\AttackerResolver();
$loggebleAttackerResolver = new \Emagia\Logger\AttackerResolverLogger(
    $resolver,
    new \Monolog\Logger('Emagia attack resolver logger'),
    \Monolog\Logger::DEBUG
);
$gamePlay = new \Emagia\GamePlayService($loggableTurn, $unitFactory, $loggebleAttackerResolver);

$gameReader = new \Emagia\Reader\GameReader();
$loggableGameReader = new \Emagia\Logger\GameplayLogger(
    $gameReader,
    new \Monolog\Logger('Emagia gameplay logger'),
    \Monolog\Logger::DEBUG);
$gamePlay->register($loggableGameReader);

$gamePlay->startBattle();
