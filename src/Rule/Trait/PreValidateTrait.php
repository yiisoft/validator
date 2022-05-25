<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use Closure;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Validator;

trait PreValidateTrait
{
    use EmptyCheckTrait;

    private function preValidate(
        $value,
        ValidationContext $context,
        bool $skipOnEmpty,
        bool $skipOnError,
        Closure $when,
    ): ?Result {
        if ($skipOnEmpty && $this->isEmpty($value)) {
            return new Result();
        }

        if ($skipOnError && $context->getParameter(Validator::PARAMETER_PREVIOUS_RULES_ERRORED) === true) {
            return new Result();
        }

        if (is_callable($when) && !($when)($value, $context)) {
            return new Result();
        }

        return null;
    }
}
