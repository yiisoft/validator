<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use InvalidArgumentException;
use ReflectionException;
use Traversable;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\Validator;
use Yiisoft\Validator\ValidatorInterface;

use function is_callable;
use function is_int;
use function is_string;

/**
 * A helper class used to normalize different types of data to the iterable with rule instances ({@see RuleInterface}).
 * Can be applied to the rules grouped by attributes with adding some default settings if needed.
 *
 * Note that when using {@see Validator}, normalization is performed automatically.
 *
 * @psalm-import-type RawRules from ValidatorInterface
 * @psalm-type NormalizedAttributeRuleGroupsArray = array<int|string, Traversable<int, RuleInterface>>
 * @psalm-type NormalizedFlatRulesIterable = iterable<int, RuleInterface>
 */
final class RulesNormalizer
{
    /**
     * Normalizes different types of data to the iterable with rule instances ({@see RuleInterface}) maintaining the
     * grouping by attributes and applying some default settings if needed.
     *
     * Based on rules source and additionally provided data this is what is done initially:
     *
     * - If rules' source is already an iterable, it will be left as is.
     * - If rules' source is not set (`null`) and validated data provided its own rules, they will be used instead.
     * - If rules' source is an object providing rules via separate method, they will be fetched and used.
     * - A single rule instance ({@see RuleInterface}) or a callable will be wrapped with array resulting in list with 1
     * item.
     * - If rules' source is a name of class providing rules via PHP attributes, they will be fetched and used.
     *
     * And then for every individual rule within a set by attribute:
     *
     * - A callable is wrapped with {@see Callback} rule.
     * - For any other type verifies that it's a valid rule instance.
     * - If default "skip on empty" condition is set, applies it if possible.
     *
     * For attributes there is an additional internal validation for being integer / string.
     *
     * @param callable|iterable|object|string|null $rules Rules source. The following types are supported:
     *
     * - Iterable (can contain single rule instances and callables for individual attribute).
     * - `null`
     * - Object providing rules via separate method.
     * - Single rule instance / callable.
     * - Name of a class providing rules via PHP attributes.
     * @psalm-param RawRules|null $rules
     *
     * @param mixed|null $data Validated data,
     * @param callable|null $defaultSkipOnEmptyCondition A default "skip on empty" condition
     * ({@see SkipOnEmptyInterface}), already normalized. Used to optimize setting the same value in all the rules.
     * Defaults to `null` meaning that it's not used.
     *
     * @throws InvalidArgumentException When attribute is neither an integer nor a string.
     * @throws ReflectionException When parsing rules from PHP attributes failed.
     *
     * @return iterable Rules normalized as a whole and individually, ready to use for validation.
     * @psalm-return NormalizedAttributeRuleGroupsArray
     */
    public static function normalize(
        callable|iterable|object|string|null $rules,
        mixed $data = null,
        ?callable $defaultSkipOnEmptyCondition = null,
    ): iterable {
        $rules = self::prepareRulesIterable($rules, $data);

        $normalizedRules = [];

        /**
         * @var mixed $attribute
         * @var mixed $attributeRules
         */
        foreach ($rules as $attribute => $attributeRules) {
            if (!is_int($attribute) && !is_string($attribute)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'An attribute can only have an integer or a string type. %s given.',
                        get_debug_type($attribute),
                    )
                );
            }

            $normalizedRules[$attribute] = new RulesNormalizerIterator(
                is_iterable($attributeRules) ? $attributeRules : [$attributeRules],
                $defaultSkipOnEmptyCondition,
            );
        }

        return $normalizedRules;
    }

    /**
     * Normalizes a set of rules using {@see RulesNormalizerIterator}. This is done for every individual rule:
     *
     * - Wrapping a callable with {@see Callback} rule.
     * - Verifying that it's a valid rule instance.
     *
     * @param callable|iterable|RuleInterface $rules A set of rules or a single rule for normalization.
     *
     * @throws InvalidArgumentException When at least one of the rules is neither a callable nor a {@see RuleInterface}
     * implementation.
     *
     * @return iterable An iterable with every rule checked and normalized.
     * @psalm-return NormalizedFlatRulesIterable
     */
    public static function normalizeList(iterable|callable|RuleInterface $rules): iterable
    {
        return new RulesNormalizerIterator(
            is_iterable($rules) ? $rules : [$rules],
        );
    }

    /**
     * Prepares rules' iterable based on provided rules' source and validated data:
     *
     * - If rules' source is already an iterable, it will be left as is.
     * - If rules' source is not set (`null`) and validated data provided its own rules, they will be used instead.
     * - If rules' source is an object providing rules via separate method, they will be fetched and used.
     * - A single rule instance ({@see RuleInterface}) or a callable will be wrapped with array resulting in list with 1
     * item.
     * - If rules' source is a name of class providing rules via PHP attributes, they will be fetched and used.
     *
     * Note that it only normalizes the type containing rules and not the rules themselves.
     *
     * @param callable|iterable|object|string|null $rules Rules source. The following types are supported:
     *
     * - Iterable.
     * - `null`
     * - Object providing rules via separate method.
     * - Single rule instance / callable.
     * - Name of a class providing rules via PHP attributes.
     * @psalm-param RawRules|null $rules
     *
     * @param mixed $data Validated data.
     *
     * @throws ReflectionException When parsing rules from PHP attributes failed.
     *
     * @return iterable An iterable with rules for further individual rules' normalization.
     */
    private static function prepareRulesIterable(
        callable|iterable|object|string|null $rules,
        mixed $data,
    ): iterable {
        if (is_iterable($rules)) {
            return $rules;
        }

        if ($rules === null) {
            return $data instanceof RulesProviderInterface
                ? $data->getRules()
                : [];
        }

        if ($rules instanceof RulesProviderInterface) {
            return $rules->getRules();
        }

        if ($rules instanceof RuleInterface || is_callable($rules)) {
            return [$rules];
        }

        return (new AttributesRulesProvider($rules))->getRules();
    }
}
