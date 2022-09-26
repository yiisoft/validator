<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\SkipOnEmptyCallback\SkipOnEmpty;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * Validates that the specified value is passed and not empty.
 */
final class RequiredHandler implements RuleHandlerInterface
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Required) {
            throw new UnexpectedRuleException(Required::class, $rule);
        }

        $result = new Result();
        if ($context->isAttributeMissing()) {
            $translatedMessage = $this->translator->translate($rule->getNotPassedMessage());
            $result->addError($translatedMessage);

            return $result;
        }

        if (is_string($value)) {
            $value = trim($value);
        }

        if (!(new SkipOnEmpty())($value, $rule, $context)) {
            return $result;
        }

        $translatedMessage = $this->translator->translate($rule->getMessage());
        $result->addError($translatedMessage);

        return $result;
    }
}
