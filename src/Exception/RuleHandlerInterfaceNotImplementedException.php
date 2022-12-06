<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Exception;

use RuntimeException;
use Throwable;
use Yiisoft\Validator\RuleHandlerContainer;
use Yiisoft\Validator\RuleHandlerInterface;

/**
 * An exception used by {@see RuleHandlerContainer} for the case when a class name was successfully retrieved from the
 * container but this class does not implement {@see RuleHandlerInterface}.
 */
final class RuleHandlerInterfaceNotImplementedException extends RuntimeException
{
    public function __construct(
        /**
         * @var string A class name retrieved from the container.
         */
        string $className,
        /**
         * @var Throwable|null The previous throwable used for the exception chaining.
         */
        ?Throwable $previous = null,
    )
    {
        parent::__construct(
            sprintf(
                'Handler "%s" must implement "%s".',
                $className,
                RuleHandlerInterface::class,
            ),
            0,
            $previous,
        );
    }
}
