<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Exception;

use RuntimeException;
use Throwable;
use Yiisoft\Validator\RuleHandlerResolver\RuleHandlerContainer;

/**
 * An exception used by {@see RuleHandlerContainer} for the case when a given class name was not found in the container.
 */
final class RuleHandlerNotFoundException extends RuntimeException
{
    public function __construct(
        /**
         * @var string A class name from failed attempt of search in the container.
         */
        string $className,
        /**
         * @var int The Exception code.
         */
        int $code = 0,
        /**
         * @var Throwable|null The previous throwable used for the exception chaining.
         */
        ?Throwable $previous = null,
    ) {
        parent::__construct(
            sprintf(
                'Handler was not found for "%s" rule or unresolved "%s" class.',
                $className,
                $className,
            ),
            $code,
            $previous,
        );
    }
}
