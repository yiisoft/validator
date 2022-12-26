<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * A handler for {@see IsTrue} rule.
 */
final class IsTrueHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof IsTrue) {
            throw new UnexpectedRuleException(IsTrue::class, $rule);
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
     * @param IsTrue $rule A rule instance.
     * @param ValidationContext $context Validation context.
     * @return array A mapping between attribute names and their values.
     * @psalm-return array<string,scalar|null>
     */
    private function getCommonResultParameters(IsTrue $rule, ValidationContext $context): array
    {
        return [
            'attribute' => $context->getTranslatedAttribute(),
            'true' => $rule->getTrueValue() === true ? 'true' : $rule->getTrueValue(),
        ];
    }
}
