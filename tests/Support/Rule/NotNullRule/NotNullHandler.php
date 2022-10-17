<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Rule\NotNullRule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class NotNullHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof NotNull) {
            throw new UnexpectedRuleException(NotNull::class, $rule);
        }

        $result = new Result();

        if ($value === null) {
            $result->addError('Values must not be null.');
        }

        return $result;
    }
}
