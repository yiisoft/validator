<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

interface RuleValidatorInterface
{
    public static function getConfigClassName(): string;

    public function validate(mixed $value, object $config, ?ValidationContext $context = null): Result;
}
