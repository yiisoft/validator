<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use Yiisoft\Validator\BeforeValidationInterface;
use Yiisoft\Validator\ValidationContext;

trait PreValidateTrait
{
    use EmptyCheckTrait;

    private string $parameterPreviousRulesErrored = 'previousRulesErrored';

    private function preValidate(
        $value,
        ValidationContext $context,
        BeforeValidationInterface $rule
    ): bool {
        if ($rule->shouldSkipOnEmpty() && $this->isEmpty($value)) {
            return true;
        }

        if ($rule->shouldSkipOnError() && $context->getParameter($this->parameterPreviousRulesErrored) === true) {
            return true;
        }

        return is_callable($rule->getWhen()) && !($rule->getWhen())($value, $context);
    }
}
