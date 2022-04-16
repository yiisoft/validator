<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Exception;

use InvalidArgumentException;

final class UnexpectedRuleException extends InvalidArgumentException
{
    public function __construct(string $expectedClassName, object $actualObject)
    {
        $actualClassName = get_class($actualObject);
        $message = "Expected \"$expectedClassName\", but {$actualClassName} given.";

        parent::__construct($message);
    }
}
