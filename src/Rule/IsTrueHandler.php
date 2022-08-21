<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * A handler for {@see IsTrue} rule.
 */
final class IsTrueHandler implements RuleHandlerInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof IsTrue) {
            throw new UnexpectedRuleException(IsTrue::class, $rule);
        }

        if ($rule->isStrict()) {
            $valid = $value === $rule->getTrueValue();
        } else {
            $valid = $value == $rule->getTrueValue();
        }

        $result = new Result();

        if ($valid) {
            return $result;
        }

        $formattedMessage = $this->translator->translate(
            $rule->getMessage(),
            [
                'true' => $rule->getTrueValue() === true ? 'true' : $rule->getTrueValue(),
                'attribute' => $context->getAttribute(),
                'value' => $value,
            ]
        );
        $result->addError($formattedMessage);

        return $result;
    }
}
