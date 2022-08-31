<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Trait;

use InvalidArgumentException;
use Yiisoft\Validator\BeforeValidationInterface;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipNone;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnEmpty;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnEmptyNormalizer;
use Yiisoft\Validator\ValidationContext;

use function is_callable;

trait PreValidateTrait
{
    use EmptyCheckTrait;

    private string $parameterPreviousRulesErrored = 'previousRulesErrored';

    private function preValidate(
        $value,
        ValidationContext $context,
        BeforeValidationInterface $rule
    ): bool {
        if (
            $rule instanceof SkipOnEmptyInterface
            && (SkipOnEmptyNormalizer::normalize($rule->getSkipOnEmpty()))($value)
        ) {
            return true;
        }

        if ($rule->shouldSkipOnError() && $context->getParameter($this->parameterPreviousRulesErrored) === true) {
            return true;
        }

        return is_callable($rule->getWhen()) && !($rule->getWhen())($value, $context);
    }
}
