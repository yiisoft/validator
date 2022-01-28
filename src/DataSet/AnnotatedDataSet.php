<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use ReflectionClass;
use Yiisoft\Validator\Attribute\HasMany;
use Yiisoft\Validator\Attribute\HasOne;
use Yiisoft\Validator\Attribute\Validate;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rules;
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
        $class = new ReflectionClass($this->baseAnnotatedObject);

        return $this->handleAnnotations($class);
    }

    private function handleAnnotations(ReflectionClass $classMeta): array
    {
        $rules = [];
        foreach ($classMeta->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            foreach ([HasMany::class, HasOne::class] as $className) {
                $attributes = $property->getAttributes($className);
                if (!$attributes) {
                    continue;
                }

                $relatedClassMeta = new ReflectionClass(new ($attributes[0]->getArguments()[0]));
                $nestedRule = Nested::rule($this->handleAnnotations($relatedClassMeta))->skipOnError(false);

                if ($className === HasMany::class) {
                    /**
                     * @psalm-suppress UndefinedMethod
                     */
                    $rules[$property->getName()][] = Each::rule(new Rules([$nestedRule]));
                } else {
                    $rules[$property->getName()] = $nestedRule;
                }
            }

            $useEach = false;
            $flatRules = [];
            $attributes = $property->getAttributes(Validate::class);
            foreach ($attributes as $index => $attribute) {
                if ($index === 0 && $attribute->getArguments()[0] === Each::class) {
                    $useEach = true;

                    continue;
                }

                $flatRules[] = $attribute->newInstance()->getRule();
            }

            if (!$flatRules) {
                continue;
            }

            $addedRules = $useEach ? Each::rule(new Rules($flatRules)) : $flatRules;
            $rules[$property->getName()] = $addedRules;
        }

        return $rules;
    }
}
