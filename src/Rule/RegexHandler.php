<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * Validates that the value matches the pattern specified in constructor.
 *
 * If the {@see Regex::$not} is used, the rule will ensure the value do NOT match the pattern.
 */
final class RegexHandler implements RuleHandlerInterface
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Regex) {
            throw new UnexpectedRuleException(Regex::class, $rule);
        }

        $result = new Result();

        if (!is_string($value)) {
            $message = $this->translator->translate(
                $rule->getIncorrectInputMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($message);

            return $result;
        }

        if (
            (!$rule->isNot() && !preg_match($rule->getPattern(), $value)) ||
            ($rule->isNot() && preg_match($rule->getPattern(), $value))
        ) {
            $message = $this->translator->translate(
                $rule->getMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($message);
        }

        return $result;
    }
}
