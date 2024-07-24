<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * A handler for {@see TrueValue} rule.
 */
final class TrueValueHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, RuleInterface $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof TrueValue) {
            throw new UnexpectedRuleException(TrueValue::class, $rule);
        }

        if (!is_scalar($value)) {
            $parameters = $this->getCommonResultParameters($rule, $context);
            $parameters['type'] = get_debug_type($value);

            return (new Result())->addError($rule->getIncorrectInputMessage(), $parameters);
        }

        if ($rule->isStrict()) {
            $valid = $value === $rule->getTrueValue();
        } else {
            $valid = $value == $rule->getTrueValue();
        }

        if ($valid) {
            return new Result();
        }

        $parameters = $this->getCommonResultParameters($rule, $context);
        $parameters['value'] = $value;

        return (new Result())->addError($rule->getMessage(), $parameters);
    }

    /**
     * @param TrueValue $rule A rule instance.
     * @param ValidationContext $context Validation context.
     *
     * @return array A mapping between attribute names and their values.
     *
     * @psalm-return array<string,scalar|null>
     */
    private function getCommonResultParameters(TrueValue $rule, ValidationContext $context): array
    {
        return [
            'property' => $context->getTranslatedProperty(),
            'Property' => $context->getCapitalizedTranslatedProperty(),
            'true' => $rule->getTrueValue() === true ? 'true' : $rule->getTrueValue(),
        ];
    }
}
