<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\ValidatorInterface;

/**
 * Validates a single value for a set of custom rules.
 */
class GroupRuleHandler implements RuleHandlerInterface
{
    public function __construct(
        private ValidatorInterface $validator,
        private ?FormatterInterface $formatter = null,
    )
    {
        $this->formatter ??= new Formatter();
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof GroupRule) {
            throw new UnexpectedRuleException(GroupRule::class, $rule);
        }

        $result = new Result();
        if (!$this->validator->validate($value, $rule->getRuleSet())->isValid()) {
            $formattedMessage = $this->formatter->format(
                $rule->getMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
        }

        return $result;
    }
}
