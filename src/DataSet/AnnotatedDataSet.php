<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use ReflectionClass;
use Yiisoft\Validator\Attribute\HasMany;
use Yiisoft\Validator\Attribute\HasOne;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\RuleSet;
use Yiisoft\Validator\RulesProviderInterface;

/**
 * This data set makes use of attributes introduced in PHP 8. It simplifies rules configuration process, especially for
 * nested data and relations. Please refer to the guide for example.
 *
 * @link https://www.php.net/manual/en/language.attributes.overview.php
 */
final class AnnotatedDataSet implements RulesProviderInterface
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

        return $this->handleAnnotations($classMeta);
    }

    private function handleAnnotations(ReflectionClass $classMeta): array
    {
        $rules = [];
        foreach ($classMeta->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            foreach ([HasMany::class, HasOne::class] as $className) {
                /**
                 * @psalm-suppress UndefinedMethod
                 */
                $attributes = $property->getAttributes($className);
                if (!$attributes) {
                    continue;
                }

                $relatedClassMeta = new ReflectionClass(new ($attributes[0]->getArguments()[0]));
                $nestedRule = new Nested($this->handleAnnotations($relatedClassMeta));

                if ($className !== HasMany::class) {
                    $rules[$property->getName()] = $nestedRule;
                } else {
                    /**
                     * @psalm-suppress UndefinedMethod
                     */
                    $rules[$property->getName()][] = new Each(new RuleSet([$nestedRule]));
                }
            }

            $flatRules = [];
            $attributes = $property->getAttributes();
            foreach ($attributes as $_index => $attribute) {
                if (!is_subclass_of($attribute->getName(), Rule::class)) {
                    continue;
                }

                $flatRules[] = $attribute->newInstance();
            }

            if (!$flatRules) {
                continue;
            }

            $rules[$property->getName()] = (string) $property->getType() === 'array'
                ? new Each(new RuleSet($flatRules))
                : $flatRules;
        }

        return $rules;
    }
}
