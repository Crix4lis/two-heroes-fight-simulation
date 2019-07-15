<?php
declare(strict_types=1);

use Monolog\Logger;

require_once 'vendor/autoload.php';

//initialize "container"
$reader = new \Emagia\Reader\GameReader();
$gameLogger = new \Emagia\Logger\GameplayLogger(new \Monolog\Logger('gameplay'), Logger::DEBUG);
$errorLogger = new \Emagia\Logger\ErrorLogger(new \Monolog\Logger('error'), Logger::ERROR);
$mediatorFactory = new \Emagia\MediatorFactory($gameLogger, $reader, $errorLogger);
$randomizer = new Emagia\Randomizer\DumbRandomizer();
$turn = new \Emagia\TurnService();
$unitFactory = new \Emagia\Unit\UnitFactory($randomizer);
$resolver = new \Emagia\AttackerResolver();
$gamePlay = new \Emagia\GamePlayService($turn, $unitFactory, $resolver, $mediatorFactory);

$gamePlay->startBattle();
