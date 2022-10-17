<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Support\Rule\StubRule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class StubRuleHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        return new Result();
    }
}
