<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use Yiisoft\Validator\Attribute\HasMany;
use Yiisoft\Validator\Attribute\HasOne;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\RulesProviderInterface;

use function in_array;

/**
 * This data set makes use of attributes introduced in PHP 8. It simplifies rules configuration process, especially for
 * nested data and relations. Please refer to the guide for example.
 *
 * @link https://www.php.net/manual/en/language.attributes.overview.php
 */
final class AttributeDataSet implements RulesProviderInterface
{
    use ArrayDataTrait;

    private object $baseAnnotatedObject;

    public function __construct(object $baseAnnotatedObject, array $data = [])
    {
        $this->baseAnnotatedObject = $baseAnnotatedObject;
        $this->data = $data;
    }

    public function getRules(): iterable
    {
        $classMeta = new ReflectionClass($this->baseAnnotatedObject);

        return $this->handleAttributes($classMeta);
    }

    private function handleAttributes(ReflectionClass $classMeta): array
    {
        $rules = [];
        foreach ($classMeta->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            foreach ([HasMany::class, HasOne::class] as $className) {
                $attributes = $property->getAttributes($className);
                if (empty($attributes)) {
                    continue;
                }

                $relatedClassName = $attributes[0]->getArguments()[0];

                try {
                    $relatedClassMeta = new ReflectionClass($relatedClassName);
                } catch (ReflectionException) {
                    throw new InvalidArgumentException("Class \"$relatedClassName\" not found.");
                }

                $nestedRule = new Nested(
                    $this->handleAttributes($relatedClassMeta),
                    ...(($property->getAttributes(Nested::class)[0] ?? null)?->getArguments() ?? [])
                );

                if ($className !== HasMany::class) {
                    $rules[$property->getName()] = $nestedRule;
                } else {
                    /** @psalm-suppress UndefinedMethod */
                    $rules[$property->getName()][] = new Each(
                        [$nestedRule],
                        ...(($property->getAttributes(Each::class)[0] ?? null)?->getArguments() ?? [])
                    );
                }
            }

            $flatRules = [];
            $attributes = $property->getAttributes();
            foreach ($attributes as $attribute) {
                if (!is_subclass_of($attribute->getName(), RuleInterface::class)) {
                    continue;
                }

                if (in_array($attribute->getName(), [Each::class, Nested::class])) {
                    continue;
                }

                $flatRules[] = $attribute->newInstance();
            }

            if (!$flatRules) {
                continue;
            }

            if ((string) $property->getType() !== 'array') {
                $rules[$property->getName()] = $flatRules;

                continue;
            }

            /** @psalm-suppress UndefinedMethod */
            $rules[$property->getName()][] = new Each(
                $flatRules,
                ...(($property->getAttributes(Each::class)[0] ?? null)?->getArguments() ?? [])
            );
        }

        return $rules;
    }
}
