<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

final class RuleWithBuiltInHandler implements RuleInterface, RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        $result = new Result();

        if ($value !== 42) {
            $result->addError('The value must be 42.');
        }

        return $result;
    }

    public function getHandler(): string|RuleHandlerInterface
    {
        return $this;
    }
}
