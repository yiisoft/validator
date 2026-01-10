<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use InvalidArgumentException;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\DumpedRuleInterface;

use function is_int;
use function is_string;
use function sprintf;

/**
 * RulesDumper allows to get an array of rule names and corresponding settings from a set of rules.
 * The array is usually passed to the client to use it in client-side validation.
 *
 * @see RuleInterface
 * @see DumpedRuleInterface
 */
final class RulesDumper
{
    /**
     * Return rules as array.
     *
     * For example:
     *
     * ```php
     * [
     *     'name' => [
     *         [
     *             'length',
     *             'min' => 4,
     *             'max' => 10,
     *             'exactly' => null,
     *             'lessThanMinMessage' => [
     *                 'template' => 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.',
     *                 'parameters' => ['min' => 4],
     *             ],
     *             'greaterThanMaxMessage' => [
     *                 'template' => 'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.',
     *                 'parameters' => ['max' => 10],
     *             ],
     *             'notExactlyMessage' => [
     *                 'template' => 'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.',
     *                 'parameters' => ['exactly' => null],
     *             ],
     *                 'incorrectInputMessage' => [
     *                 'template' => 'The value must be a string.',
     *                 'parameters' => [],
     *             ],
     *             'encoding' => 'UTF-8',
     *             'skipOnEmpty' => false,
     *             'skipOnError' => false,
     *         ],
     *         [
     *             'callback',
     *         ],
     *     ],
     *     // ...
     * ],
     * ```
     *
     * @param iterable $rules Arrays of rule objects indexed by properties.
     *
     * @return array Array of rule names and corresponding settings indexed by properties.
     */
    public static function asArray(iterable $rules): array
    {
        return self::fetchOptions($rules);
    }

    /**
     * Converts rule objects to arrays of rule names and corresponding settings.
     *
     * @param iterable $rules Arrays of rule objects indexed by properties.
     *
     * @return array Array of rule names and corresponding settings indexed by properties.
     */
    private static function fetchOptions(iterable $rules): array
    {
        $result = [];
        /**
         * @var mixed $property
         * @var mixed $rule
         */
        foreach ($rules as $property => $rule) {
            if (!is_int($property) && !is_string($property)) {
                $message = sprintf(
                    'A property can only have an integer or a string type. %s given.',
                    get_debug_type($property),
                );

                throw new InvalidArgumentException($message);
            }

            if (is_iterable($rule)) {
                $options = self::fetchOptions($rule);
            } elseif ($rule instanceof DumpedRuleInterface) {
                $options = array_merge([$rule->getName()], $rule->getOptions());
            } elseif ($rule instanceof RuleInterface) {
                $options = [$rule::class];
            } else {
                throw new InvalidArgumentException(sprintf(
                    'Every rule must implement "%s". Type "%s" given.',
                    RuleInterface::class,
                    get_debug_type($rule),
                ));
            }

            $result[$property] = $options;
        }

        return $result;
    }
}
