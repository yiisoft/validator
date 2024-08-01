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
 * Validates that a minimum number of specified properties are filled.
 *
 * @see FilledAtLeast
 */
final class FilledAtLeastHandler implements RuleHandlerInterface
{
    use TranslatedPropertiesHandlerTrait;

    public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof FilledAtLeast) {
            throw new UnexpectedRuleException(FilledAtLeast::class, $rule);
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
        }

        if ($filledCount < $rule->getMin()) {
            $result->addError($rule->getMessage(), [
                'property' => $context->getTranslatedProperty(),
                'Property' => $context->getCapitalizedTranslatedProperty(),
                'properties' => $this->getFormattedPropertiesString($rule->getProperties(), $context),
                'Properties' => $this->getCapitalizedPropertiesString($rule->getProperties(), $context),
                'min' => $rule->getMin(),
            ]);
        }

        return $result;
    }
}
