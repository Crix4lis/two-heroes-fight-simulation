<?php
declare(strict_types=1);

require_once 'vendor/autoload.php';

//initialize "container"
$logger = new \Monolog\Logger('Emagia logger');
$randomizer = new Emagia\Randomizer\DumbRandomizer();
$turn = new \Emagia\TurnService();
$loggableTurn = new \Emagia\Logger\TurnServiceLogger($turn, $logger, \Monolog\Logger::DEBUG);
$unitFactory = new \Emagia\Unit\UnitFactory($randomizer);
$resolver = new \Emagia\AttackerResolver();
$loggebleAttackerResolver = new \Emagia\Logger\AttackerResolverLogger($resolver, $logger, \Monolog\Logger::DEBUG);
$gamePlay = new \Emagia\GamePlayService($loggableTurn, $unitFactory, $loggebleAttackerResolver);

$gameReader = new \Emagia\Reader\GameReader();
$loggableGameReader = new \Emagia\Logger\EventLogger($gameReader, $logger, \Monolog\Logger::DEBUG);
$gamePlay->register($loggableGameReader);

$gamePlay->startBattle();
