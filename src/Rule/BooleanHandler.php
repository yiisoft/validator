<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Checks if the value is a boolean value or a value corresponding to it.
 */
final class BooleanHandler implements RuleHandlerInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Boolean) {
            throw new UnexpectedRuleException(Boolean::class, $rule);
        }

        if ($rule->isStrict()) {
            $valid = $value === $rule->getTrueValue() || $value === $rule->getFalseValue();
        } else {
            $valid = $value == $rule->getTrueValue() || $value == $rule->getFalseValue();
        }

        $result = new Result();

        if ($valid) {
            return $result;
        }

        $message = $this->translator->translate(
            $rule->getMessage(),
            [
                'true' => $rule->getTrueValue() === true ? '1' : $rule->getTrueValue(),
                'false' => $rule->getFalseValue() === false ? '0' : $rule->getFalseValue(),
                'attribute' => $context?->getAttribute(),
                'value' => $value,
            ]
        );
        $result->addError($message);

        return $result;
    }
}
