<?php
declare(strict_types=1);

namespace Emagia\Randomizer;

class DumbRandomizer implements RandomizerInterface
{
    public function randomize(int $from, int $to): int
    {
        return random_int($from, $to);
    }
}
