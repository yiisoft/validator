<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;

interface RuleValidatorInterface
{
    public static function getConfigClassName(): string;

    public function validate(mixed $value, object $config, ValidatorInterface $validator, ?ValidationContext $context = null): Result;
}
