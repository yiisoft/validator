<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use InvalidArgumentException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule\Trait\FormatMessageTrait;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;
use Yiisoft\Validator\Exception\UnexpectedRuleException;

/**
 * Validates an array by checking each of its elements against a set of rules.
 */
final class EachHandler implements RuleHandlerInterface
{
    use FormatMessageTrait;

    public function validate(mixed $value, object $rule, ?ValidationContext $context = null): Result
    {
        if (!$rule instanceof Each) {
            throw new UnexpectedRuleException(Each::class, $rule);
        }

        $rules = $rule->getRules();
        if ($rules === []) {
            throw new InvalidArgumentException('Rules are required.');
        }

        $result = new Result();
        if (!is_iterable($value)) {
            $formattedMessage = $this->formatMessage(
                $rule->getIncorrectInputMessage(),
                ['attribute' => $context?->getAttribute(), 'value' => $value]
            );
            $result->addError($formattedMessage);

            return $result;
        }

        foreach ($value as $index => $item) {
            /** @var array<mixed, RuleInterface[]> $rule */
            $rule = [$index => $rules];
            $itemResult = $context?->getValidator()->validate($item, $rule);
            if ($itemResult->isValid()) {
                continue;
            }

            foreach ($itemResult->getErrors() as $error) {
                $result->addError($error->getMessage(), $error->getValuePath());
            }
        }

        return $result;
    }
}
