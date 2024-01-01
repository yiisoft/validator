<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\EmptyCondition\WhenEmpty;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Trait\TranslatedAttributesHandlerTrait;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_array;
use function is_object;

/**
 * Validates that one of specified attributes is filled.
 *
 * @see OneOf
 */
final class OneOfHandler implements RuleHandlerInterface
{
    use TranslatedAttributesHandlerTrait;

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof OneOf) {
            throw new UnexpectedRuleException(OneOf::class, $rule);
        }

        /** @var mixed $value */
        $value = $context->getParameter(ValidationContext::PARAMETER_VALUE_AS_ARRAY) ?? $value;

        $result = new Result();

        if (!is_array($value) && !is_object($value)) {
            return $result->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'label' => $context->getTranslatedLabel(),
                'type' => get_debug_type($value),
            ]);
        }

        $filledCount = 0;
        foreach ($rule->getAttributes() as $attribute) {
            if (!(new WhenEmpty())(ArrayHelper::getValue($value, $attribute), $context->isAttributeMissing())) {
                $filledCount++;
            }

            if ($filledCount > 1) {
                return $this->getGenericErrorResult($rule, $context);
            }
        }

        return $filledCount === 1 ? $result : $this->getGenericErrorResult($rule, $context);
    }

    private function getGenericErrorResult(OneOf $rule, ValidationContext $context): Result
    {
        return (new Result())->addError($rule->getMessage(), [
            'attributes' => $this->getFormattedAttributesString($rule->getAttributes(), $context),
        ]);
    }
}
