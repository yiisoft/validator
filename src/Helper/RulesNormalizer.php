<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use InvalidArgumentException;
use ReflectionException;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\ValidatorInterface;

use function is_callable;
use function is_int;
use function is_string;

/**
 * @psalm-import-type RulesType from ValidatorInterface
 */
final class RulesNormalizer
{
    /**
     * @psalm-param RulesType $rules
     *
     * @throws InvalidArgumentException
     * @throws ReflectionException
     *
     * @return iterable<int|string, iterable<int, RuleInterface>>
     */
    public static function normalize(
        callable|iterable|object|string|null $rules,
        mixed $data = null,
        ?callable $defaultSkipOnEmptyCriteria = null,
    ): iterable {
        $rules = self::prepareRulesArray($rules, $data);

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

            $normalizedRules[$attribute] = self::normalizeRulesGenerator(
                is_iterable($attributeRules) ? $attributeRules : [$attributeRules],
                $defaultSkipOnEmptyCriteria
            );
        }

        return $normalizedRules;
    }

    /**
     * @throws InvalidArgumentException
     *
     * @return iterable<int, RuleInterface>
     */
    public static function normalizeList(iterable|callable|RuleInterface $rules): iterable
    {
        return self::normalizeRulesGenerator(
            is_iterable($rules) ? $rules : [$rules]
        );
    }

    /**
     * @psalm-param RulesType $rules
     *
     * @throws ReflectionException
     */
    private static function prepareRulesArray(
        callable|iterable|object|string|null $rules,
        mixed $data,
    ): iterable {
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

        if (is_iterable($rules)) {
            return $rules;
        }

        return (new AttributesRulesProvider($rules))->getRules();
    }

    /**
     * @throws InvalidArgumentException
     *
     * @return iterable<int, RuleInterface>
     */
    private static function normalizeRulesGenerator(
        iterable $rules,
        ?callable $defaultSkipOnEmptyCriteria = null
    ): iterable {
        /** @var mixed $rule */
        foreach ($rules as $rule) {
            yield self::normalizeRule($rule, $defaultSkipOnEmptyCriteria);
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private static function normalizeRule(mixed $rule, ?callable $defaultSkipOnEmptyCriteria): RuleInterface
    {
        if (is_callable($rule)) {
            return new Callback($rule);
        }

        if (!$rule instanceof RuleInterface) {
            throw new InvalidArgumentException(
                sprintf(
                    'Rule should be either an instance of %s or a callable, %s given.',
                    RuleInterface::class,
                    get_debug_type($rule)
                )
            );
        }

        if (
            $defaultSkipOnEmptyCriteria !== null
            && $rule instanceof SkipOnEmptyInterface
            && $rule->getSkipOnEmpty() === null
        ) {
            $rule = $rule->skipOnEmpty($defaultSkipOnEmptyCriteria);
        }

        return $rule;
    }
}
