<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Helper;

use ReflectionException;
use ReflectionProperty;
use Traversable;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesProvider\AttributesRulesProvider;
use Yiisoft\Validator\RulesProviderInterface;
use Yiisoft\Validator\ValidatorInterface;

use function is_object;
use function is_string;

/**
 * @psalm-import-type RulesType from ValidatorInterface
 */
final class RulesNormalizer
{
    /**
     * @psalm-param RulesType $rules
     *
     * @throws ReflectionException
     */
    public static function normalize(
        iterable|object|string|null $rules,
        int $propertyVisibility = ReflectionProperty::IS_PRIVATE
        | ReflectionProperty::IS_PROTECTED
        | ReflectionProperty::IS_PUBLIC,
        mixed $data = null,
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

        if (is_string($rules)) {
            return self::getRulesFromAttributes($rules, $propertyVisibility);
        }

        if (is_object($rules)) {
            return $rules instanceof Traversable
                ? $rules
                : self::getRulesFromAttributes($rules, $propertyVisibility);
        }

        return $rules;
    }

    /**
     * @param class-string|object $source
     *
     * @throws ReflectionException
     */
    private static function getRulesFromAttributes(string|object $source, int $propertyVisibility): iterable
    {
        return (new AttributesRulesProvider($source, $propertyVisibility))->getRules();
    }
}
