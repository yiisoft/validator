<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use InvalidArgumentException;
use Yiisoft\Validator\Rule\Callback;
use Yiisoft\Validator\RuleInterface;
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
     *
     * @return iterable<int|string, iterable<int|string, RuleInterface>>
     */
    public static function normalize(
        iterable|object|null $rules,
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

            $normalizedRules[$attribute] = self::normalizeAttributeRules(
                is_iterable($attributeRules) ? $attributeRules : [$attributeRules],
                $defaultSkipOnEmptyCriteria
            );
        }

        return $normalizedRules;
    }

    /**
     * @psalm-param RulesType $rules
     *
     * @throws InvalidArgumentException
     */
    private static function prepareRulesArray(
        iterable|object|null $rules,
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

        if ($rules instanceof RuleInterface) {
            return [$rules];
        }

        /** @psalm-suppress RedundantConditionGivenDocblockType */
        if (is_iterable($rules)) {
            return $rules;
        }

        throw new InvalidArgumentException('A rules object should implement RulesProviderInterface or RuleInterface.');
    }

    /**
     * @throws InvalidArgumentException
     *
     * @return iterable<int|string, RuleInterface>
     */
    private static function normalizeAttributeRules(iterable $rules, ?callable $defaultSkipOnEmptyCriteria): iterable
    {
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

        if ($rule instanceof SkipOnEmptyInterface && $rule->getSkipOnEmpty() === null) {
            $rule = $rule->skipOnEmpty($defaultSkipOnEmptyCriteria);
        }

        return $rule;
    }
}
