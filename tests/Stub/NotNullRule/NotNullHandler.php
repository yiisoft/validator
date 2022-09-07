<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub\NotNullRule;

use Yiisoft\Translator\TranslatorInterface;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

final class NotNullHandler implements RuleHandlerInterface
{
    public function __construct(private TranslatorInterface $translator)
    {
    }

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof NotNull) {
            throw new UnexpectedRuleException(NotNull::class, $rule);
        }

        $result = new Result();

        if ($value === null) {
            $message = $this->translator->translate('Values must not be null.');
            $result->addError($message);
        }

        return $result;
    }
}
