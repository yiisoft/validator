<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\EmptyCriteria\WhenEmpty;
use Yiisoft\Validator\ValidationContext;

use function is_array;
use function is_object;

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

        $result = new Result();

        if (!is_array($value) && !is_object($value)) {
            return $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);
        }

        $filledCount = 0;
        foreach ($rule->getAttributes() as $attribute) {
            if (!(new WhenEmpty())(ArrayHelper::getValue($value, $attribute), $context->isAttributeMissing())) {
                $filledCount++;
            }
        }

        if ($filledCount < $rule->getMin()) {
            $result->addError($rule->getMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'min' => $rule->getMin(),
            ]);
        }

        return $result;
    }
}
