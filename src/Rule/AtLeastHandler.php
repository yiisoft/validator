<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnEmpty;
use Yiisoft\Validator\ValidationContext;

/**
 * Checks if at least {@see AtLeast::$min} of many attributes are filled.
 */
final class AtLeastHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof AtLeast) {
            throw new UnexpectedRuleException(AtLeast::class, $rule);
        }

        $filledCount = 0;

        foreach ($rule->getAttributes() as $attribute) {
            if (!(new SkipOnEmpty())(ArrayHelper::getValue($value, $attribute), $context->isAttributeMissing())) {
                $filledCount++;
            }
        }

        $result = new Result();

        if ($filledCount < $rule->getMin()) {
            $result->addError(
                $rule->getMessage(),
                [
                    'min' => $rule->getMin(),
                    'attribute' => $context->getAttribute(),
                    'value' => $value,
                ],
            );
        }

        return $result;
    }
}
