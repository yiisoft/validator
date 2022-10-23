<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use InvalidArgumentException;

/**
 * RulesDumper allows to get an array of rule names and corresponding settings from a set of rules.
 * The array is usually passed to the client to use it in client-side validation.
 *
 * * @see SerializableRuleInterface
 * * @see RuleInterface
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
     *            'notANumberMessage' => 'Value must be an integer.',
     *            'tooBigMessage' => 'Value must be no greater than 100.'
     *        ],
     *        ['callback'],
     *    ],
     *    'name' => [
     *        [
     *            'hasLength',
     *            'max' => 20,
     *            'message' => 'Value must contain at most 20 characters.'
     *        ],
     *    ],
     * ]
     * ```
     *
     * @param iterable $rules
     */
    public function asArray(iterable $rules): array
    {
        return $this->fetchOptions($rules);
    }

    private function fetchOptions(iterable $rules): array
    {
        $result = [];
        foreach ($rules as $attribute => $rule) {
            if (is_iterable($rule)) {
                $result[$attribute] = $this->fetchOptions($rule);
            } elseif ($rule instanceof SerializableRuleInterface) {
                $result[$attribute] = array_merge([$rule->getName()], $rule->getOptions());
            } elseif ($rule instanceof RuleInterface) {
                $result[$attribute] = [$rule->getName()];
            } else {
                throw new InvalidArgumentException(sprintf(
                    'Each rule must implement "%s". Type "%s" given.',
                    RuleInterface::class,
                    get_debug_type($rule),
                ));
            }
        }

        return $result;
    }
}
