<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Exception;

use RuntimeException;
use Throwable;
use Yiisoft\Validator\RuleHandlerResolver\RuleHandlerContainer;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleHandlerResolverInterface;

/**
 * An exception used by {@see RuleHandlerResolverInterface} implementations (e. g., {@see RuleHandlerContainer}) for
 * the case when a retrieved value is not an object or an object that does not implement {@see RuleHandlerInterface}.
 */
final class RuleHandlerInterfaceNotImplementedException extends RuntimeException
{
    public function __construct(
        /**
         * @param mixed A variable retrieved from the container.
         */
        mixed $value,
        /**
         * @var Throwable|null The previous throwable used for the exception chaining.
         */
        ?Throwable $previous = null,
    ) {
        $type = get_debug_type($value);

        parent::__construct(
            sprintf(
                class_exists($type)
                    ? 'Handler "%1$s" must implement "%2$s".'
                    : 'Expected instance of "%2$s". Got "%1$s".',
                $type,
                RuleHandlerInterface::class,
            ),
            0,
            $previous
        );
    }
}
