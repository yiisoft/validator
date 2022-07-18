<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Trait\EmptyCheckTrait;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Checks if at least {@see AtLeast::$min} of many attributes are filled.
 */
final class AtLeastHandler implements RuleHandlerInterface
{
    use EmptyCheckTrait;

    private FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new Formatter();
    }

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof AtLeast) {
            throw new UnexpectedRuleException(AtLeast::class, $rule);
        }

        $filledCount = 0;

        foreach ($rule->getAttributes() as $attribute) {
            if (!$this->isEmpty($value->{$attribute})) {
                $filledCount++;
            }
        }

        $result = new Result();

        if ($filledCount < $rule->getMin()) {
            $formattedMessage = $this->formatter->format(
                $rule->getMessage(),
                ['min' => $rule->getMin(), 'attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);
        }

        return $result;
    }
}
