<?php
declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Closure;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleSet;
use Yiisoft\Validator\ValidationContext;

trait PreValidateTrait
{
    use EmptyCheckTrait;

    private function preValidate(
        $value,
        ValidationContext $context,
        bool $skipOnEmpty,
        bool $skipOnError,
        Closure $when,
    )
    {
        if ($skipOnEmpty && $this->isEmpty($value)) {
            return new Result();
        }

        if ($skipOnError && $context->getParameter(RuleSet::PARAMETER_PREVIOUS_RULES_ERRORED) === true) {
            return new Result();
        }

        if (is_callable($when) && !($when)($value, $context)) {
            return new Result();
        }

        return null;
    }
}
