<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use DateTimeInterface;
use Stringable;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function count;
use function gettype;

/**
 * A handler for {@see UniqueIterable} rule. Validates uniqueness of each element of an iterable.
 */
final class UniqueIterableHandler implements RuleHandlerInterface
{
    public function validate(mixed $value, object $rule, ValidationContext $context): Result
    {
        if (!$rule instanceof UniqueIterable) {
            throw new UnexpectedRuleException(UniqueIterable::class, $rule);
        }

        if (!is_iterable($value)) {
            return (new Result())->addError($rule->getIncorrectInputMessage(), [
                'attribute' => $context->getTranslatedAttribute(),
                'type' => get_debug_type($value),
            ]);
        }

        $stack = [];
        $previousItem = null;
        foreach ($value as $item) {
            if (!$this->isValueAllowedForItem($item)) {
                return (new Result())->addError($rule->getIncorrectItemValueMessage(), [
                    'attribute' => $context->getTranslatedAttribute(),
                    'type' => get_debug_type($item),
                ]);
            }

            if ($previousItem !== null && gettype($previousItem) !== gettype($item)) {
                return (new Result())->addError($rule->getDifferentTypesMessage(), [
                    'attribute' => $context->getTranslatedAttribute(),
                ]);
            }

            $previousItem = $item;

            if (!empty($stack) && count($stack) !== count(array_unique($stack, flags: SORT_REGULAR))) {
                return (new Result())->addError($rule->getMessage(), [
                    'attribute' => $context->getTranslatedAttribute(),
                ]);
            }

            if ($value instanceof Stringable) {
                $stack[] = (string) $value;
            } elseif ($value instanceof DateTimeInterface) {
                $stack[] = $value->getTimestamp();
            } else {
                $stack[] = $value;
            }
        }

        return new Result();
    }

    /**
     * @psalm-assert string|int|float|bool|Stringable|DateTimeInterface $value
     */
    private function isValueAllowedForItem(mixed $value): bool
    {
        return is_scalar($value) || $value instanceof Stringable || $value instanceof DateTimeInterface;
    }
}
