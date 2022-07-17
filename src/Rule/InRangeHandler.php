<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\FallbackTranslator;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;

/**
 * Validates that the value is among a list of values.
 *
 * The range can be specified via constructor.
 * If the {@see InRange::$not} is called, the rule will ensure the value is NOT among the specified range.
 */
final class InRangeHandler implements RuleHandlerInterface
{
    private TranslatorInterface $translator;

    public function __construct(?TranslatorInterface $translator = null)
    {
        $this->translator = $translator ?? new FallbackTranslator();
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof InRange) {
            throw new UnexpectedRuleException(InRange::class, $rule);
        }

        $result = new Result();

        if ($value === null && $rule->shouldSkipOnEmpty()) {
            return $result;
        }

        if ($rule->isNot() === ArrayHelper::isIn($value, $rule->getRange(), $rule->isStrict())) {
            $message = $this->translator->translate(
                $rule->getMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($message);
        }

        return $result;
    }
}
