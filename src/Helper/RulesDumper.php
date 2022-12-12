<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use InvalidArgumentException;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RuleWithOptionsInterface;

use function is_int;
use function is_string;

/**
 * RulesDumper allows to get an array of rule names and corresponding settings from a set of rules.
 * The array is usually passed to the client to use it in client-side validation.
 *
 * @see RuleInterface
 * @see RuleWithOptionsInterface
 */
final class RulesDumper
{
    /**
     * Return all attribute rules as array.
     *
     * For example:
     *
     * ```php
     * [
     *    'amount' => [
     *        [
     *            'number',
     *            'integer' => true,
     *            'max' => 100,
     *            'notNumberMessage' => ['template' => 'Value must be an integer.', 'parameters' => []],
     *            'tooBigMessage' => ['template' => 'Value must be no greater than 100.', 'parameters' => []],
     *        ],
     *        ['callback'],
     *    ],
     *    'name' => [
     *        [
     *            'hasLength',
     *            'max' => 20,
     *            'message' => ['template' => 'Value must contain at most 20 characters.', 'parameters' => []],
     *        ],
     *    ],
     * ]
     * ```
     */
    public function asArray(iterable $rules): array
    {
        return $this->fetchOptions($rules);
    }

    private function fetchOptions(iterable $rules): array
    {
        $result = [];
        /** @var mixed $attribute */
        /** @var mixed $rule */
        foreach ($rules as $attribute => $rule) {
            if (!is_int($attribute) && !is_string($attribute)) {
                $message = sprintf(
                    'An attribute can only have an integer or a string type. %s given.',
                    get_debug_type($attribute),
                );

                throw new InvalidArgumentException($message);
            }

            if (is_iterable($rule)) {
                $options = $this->fetchOptions($rule);
            } elseif ($rule instanceof RuleWithOptionsInterface) {
                $options = array_merge([$rule->getName()], $rule->getOptions());
            } elseif ($rule instanceof RuleInterface) {
                $options = [$rule->getName()];
            } else {
                throw new InvalidArgumentException(sprintf(
                    'Every rule must implement "%s". Type "%s" given.',
                    RuleInterface::class,
                    get_debug_type($rule),
                ));
            }

            $result[$attribute] = $options;
        }

        return $result;
    }
}
