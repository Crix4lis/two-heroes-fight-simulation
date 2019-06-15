<?php
declare(strict_types=1);

namespace Emagia\Logger;

use Emagia\AttackerResolverInterface;
use Emagia\AttackResolverException;
use Emagia\Unit\UnitInterface;
use Monolog\Logger as MonoLogger;

class AttackerResolverLogger extends BaseLogger implements AttackerResolverInterface
{
    /** @var AttackerResolverInterface */
    private $resolver;

    public function __construct(
        AttackerResolverInterface $resolver,
        MonoLogger $logger,
        int $level = MonoLogger::ERROR,
        string $logFilePath = BaseLogger::DEFAULT_FILE
    )
    {
        parent::__construct($logger, $level, $logFilePath);
        $this->resolver = $resolver;
    }

    /**
     * @param UnitInterface $firstUnit
     * @param UnitInterface $secondUnit
     *
     * @return UnitInterface
     *
     * @throws AttackResolverException
     */
    public function resolveAttacker(UnitInterface $firstUnit, UnitInterface $secondUnit): UnitInterface
    {
        try {
            return $this->resolver->resolveAttacker($firstUnit, $secondUnit);
        } catch (AttackResolverException $e) {
            $this->logger->error($e->getMessage());
            throw $e;
        }
    }
}
