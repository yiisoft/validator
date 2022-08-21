<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use InvalidArgumentException;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Formatter;
use Yiisoft\Validator\FormatterInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

use function is_array;

/**
 * Validates an array by checking each of its elements against a set of rules.
 */
final class EachHandler implements RuleHandlerInterface
{
    private FormatterInterface $formatter;

    public function __construct(?FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new Formatter();
    }

    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Each) {
            throw new UnexpectedRuleException(Each::class, $rule);
        }

        /** @var Each $eachRule */
        $eachRule = $rule;

        $rules = $rule->getRules();
        if ($rules === []) {
            throw new InvalidArgumentException('Rules are required.');
        }

        $result = new Result();
        if (!is_iterable($value)) {
            $formattedMessage = $this->formatter->format(
                $rule->getIncorrectInputMessage(),
                ['attribute' => $context->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);

            return $result;
        }

        foreach ($value as $index => $item) {
            /** @var array<mixed, \Closure|\Closure[]|RuleInterface|RuleInterface[]> $rule */
            $rule = [$index => $rules];
            $itemResult = $context->getValidator()->validate($item, $rule);
            if ($itemResult->isValid()) {
                continue;
            }

            foreach ($itemResult->getErrors() as $error) {
                if (!is_array($item)) {
                    $errorKey = [$index];
                    $formatMessage = true;
                } else {
                    $errorKey = [$index, ...$error->getValuePath()];
                    $formatMessage = false;
                }

                $message = !$formatMessage ? $error->getMessage() : $this->formatter->format($eachRule->getMessage(), [
                    'error' => $error->getMessage(),
                    'value' => $item,
                ]);
                $result->addError($message, $errorKey);
            }
        }

        return $result;
    }
}
