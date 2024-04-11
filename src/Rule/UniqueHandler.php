<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use DateTimeInterface;
use Stringable;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * A handler for {@see Unique} rule. Validates uniqueness of each element of an iterable.
 */
final class UniqueHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof Unique) {
            throw new UnexpectedRuleException(Unique::class, $rule);
        }

        if (!is_iterable($value)) {
            return (new Result())->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);
        }

        $stack = [];
        foreach ($value as $item) {
            if (!$this->isValueAllowedForItem($item)) {
                return (new Result())->addError($rule->getIncorrectItemValueMessage(), [
                    'attribute' => $context->getTranslatedAttribute(),
                    'type' => get_debug_type($item),
                ]);
            }

            foreach ($stack as $stackItem) {
                if ($this->areItemsEqual($item, $stackItem)) {
                    return (new Result())->addError($rule->getMessage(), [
                        'attribute' => $context->getTranslatedAttribute(),
                    ]);
                }
            }

            $stack[] = $item;
        }

        return new Result();
    }

    /**
     * @psalm-assert null|string|int|float|bool|Stringable|DateTimeInterface $value
     */
    private function isValueAllowedForItem(mixed $value): bool
    {
        return $value === null ||
            is_scalar($value) ||
            $value instanceof Stringable ||
            $value instanceof DateTimeInterface;
    }

    private function areItemsEqual(
        null|string|int|float|bool|Stringable|DateTimeInterface $item,
        null|string|int|float|bool|Stringable|DateTimeInterface $stackItem,
    ): bool {
        if ($item instanceof DateTimeInterface && $stackItem instanceof DateTimeInterface) {
            return $item == $stackItem;
        }

        if ($item instanceof Stringable) {
            $item = (string) $item;
        }

        if ($stackItem instanceof Stringable) {
            $stackItem = (string) $stackItem;
        }

        return $item === $stackItem;
    }
}
