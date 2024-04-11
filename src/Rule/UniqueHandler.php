<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use DateTime;
use DateTimeInterface;
use Stringable;
use Yiisoft\Validator\Exception\UnexpectedRuleException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\RuleHandlerInterface;
use Yiisoft\Validator\ValidationContext;

use function in_array;

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

        $stackMap = ['simple' => [], 'datetime' => []];
        foreach ($value as $item) {
            if (!$this->isValueAllowedForItem($item)) {
                return (new Result())->addError($rule->getIncorrectItemValueMessage(), [
                    'attribute' => $context->getTranslatedAttribute(),
                    'type' => get_debug_type($item),
                ]);
            }

            if ($item instanceof Stringable) {
                $itemValue = (string) $item;
                $stackMapKey = 'simple';
            } elseif ($item instanceof DateTimeInterface) {
                $itemValue = $item->getTimestamp();
                $stackMapKey = 'datetime';
            } else {
                $itemValue = $item;
                $stackMapKey = 'simple';
            }

            if (in_array($itemValue, $stackMap[$stackMapKey], strict: true)) {
                return (new Result())->addError($rule->getMessage(), [
                    'attribute' => $context->getTranslatedAttribute(),
                ]);
            }

            $stackMap[$stackMapKey][] = $itemValue;
        }

        return new Result();
    }

    private function isValueAllowedForItem(mixed $value): bool
    {
        return $value === null ||
            is_scalar($value) ||
            $value instanceof Stringable ||
            $value instanceof DateTimeInterface;
    }
}
