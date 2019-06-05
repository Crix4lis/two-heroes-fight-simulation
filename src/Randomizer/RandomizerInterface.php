<?php
declare(strict_types=1);

namespace Emagia\Randomizer;

interface RandomizerInterface
{
    public function randomize(int $from, int $to): int;
}
