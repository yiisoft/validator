<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use InvalidArgumentException;
use function is_array;

/**
 * RulesDumper allows to get an array of rule names and corresponding settings from a set of rules.
 * The array is usually passed to the client to use it in client-side validation.
 *
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
     * @param iterable $ruleSetMap
     *
     * @return array
     */
    public function asArray(iterable $ruleSetMap): array
    {
        $arrayMap = [];
        foreach ($ruleSetMap as $attribute => $ruleSet) {
            if (is_array($ruleSet)) {
                $ruleSet = new RuleSet($ruleSet);
            }

            if (!$ruleSet instanceof RuleSet) {
                throw new InvalidArgumentException(sprintf(
                    'Value should be an instance of %s or an array of rules, %s given.',
                    RuleSet::class,
                    get_debug_type($ruleSet)
                ));
            }

            $arrayMap[$attribute] = $ruleSet->asArray();
        }

        return $arrayMap;
    }
}
