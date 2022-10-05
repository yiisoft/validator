<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Exception;

use InvalidArgumentException;

final class UnexpectedRuleException extends InvalidArgumentException
{
    public function __construct(string $expectedClassName, object $actualObject)
    {
        $actualClassName = $actualObject::class;
        $message = "Expected \"$expectedClassName\", but {$actualClassName} given.";

        parent::__construct($message);
    }
}
