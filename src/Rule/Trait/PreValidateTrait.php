<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnEmptyNormalizer;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\WhenInterface;

trait PreValidateTrait
{
    private string $parameterPreviousRulesErrored = 'previousRulesErrored';

    private function preValidate(
        $value,
        ValidationContext $context,
        RuleInterface $rule
    ): bool {
        if (
            $rule instanceof SkipOnEmptyInterface &&
            (SkipOnEmptyNormalizer::normalize($rule->getSkipOnEmpty()))($value, $context->isAttributeMissing())
        ) {
            return true;
        }

        if (
            $rule instanceof SkipOnErrorInterface
            && $rule->shouldSkipOnError()
            && $context->getParameter($this->parameterPreviousRulesErrored) === true
        ) {
            return true;
        }

        if ($rule instanceof WhenInterface) {
            $when = $rule->getWhen();
            return $when !== null && !$when($value, $context);
        }

        return false;
    }
}
