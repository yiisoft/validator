<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\EmptyCondition\WhenEmpty;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Trait\TranslatedPropertiesHandlerTrait;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

use function is_array;
use function is_object;

/**
 * Validates that one of specified properties is filled.
 *
 * @see OneOf
 */
final class OneOfHandler implements RuleHandlerInterface
{
    use TranslatedPropertiesHandlerTrait;

    public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof OneOf) {
            throw new UnexpectedRuleException(OneOf::class, $rule);
        }

        /** @var mixed $value */
        $value = $context->getParameter(ValidationContext::PARAMETER_VALUE_AS_ARRAY) ?? $value;

        $result = new Result();

        if (!is_array($value) && !is_object($value)) {
            return $result->addError($rule->getIncorrectInputMessage(), [
                'property' => $context->getTranslatedProperty(),
                'Property' => $context->getCapitalizedTranslatedProperty(),
                'type' => get_debug_type($value),
            ]);
        }

        $filledCount = 0;
        foreach ($rule->getProperties() as $property) {
            if (!(new WhenEmpty())(ArrayHelper::getValue($value, $property), $context->isPropertyMissing())) {
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
            'property' => $context->getTranslatedProperty(),
            'Property' => $context->getCapitalizedTranslatedProperty(),
            'properties' => $this->getFormattedPropertiesString($rule->getProperties(), $context),
            'Properties' => $this->getCapitalizedPropertiesString($rule->getProperties(), $context),
        ]);
    }
}
