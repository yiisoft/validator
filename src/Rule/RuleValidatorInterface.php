<?php

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

interface RuleValidatorInterface
{
    public function validate(mixed $value, object $config, ?ValidationContext $context = null): Result;
}
