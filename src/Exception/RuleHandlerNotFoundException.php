<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Exception;

use RuntimeException;
use Throwable;

final class RuleHandlerNotFoundException extends RuntimeException
{
    public function __construct(string $name, ?Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                'Handler was not found for "%s" rule or unresolved "%s" class.',
                $name,
                $name
            ),
            0,
            $previous
        );
    }
}
