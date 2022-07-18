<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Exception;

use RuntimeException;
use Throwable;
use Yiisoft\Validator\RuleHandlerInterface;

final class RuleHandlerInterfaceNotImplementedException extends RuntimeException
{
    public function __construct(string $name, ?Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                'Handler "%s" should implement "%s".',
                $name,
                RuleHandlerInterface::class
            ),
            0,
            $previous
        );
    }
}
