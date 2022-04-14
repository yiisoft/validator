<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\Rule\ValidatorClassNameTrait;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;

final class RuleValidator implements RuleValidatorInterface
{
    use ValidatorClassNameTrait;

    public function validate(mixed $value, object $rule, ValidatorInterface $validator, ?ValidationContext $context = null): Result
    {
        return new Result();
    }
}
