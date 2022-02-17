<?php

declare(strict_types=1);

namespace Yiisoft\Validator\DataSet;

use InvalidArgumentException;
use ReflectionClass;
use Yiisoft\Validator\Attribute\HasMany;
use Yiisoft\Validator\Attribute\HasOne;
use Yiisoft\Validator\Attribute\Validate;
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
                $nestedRule = Nested::rule($this->handleAnnotations($relatedClassMeta))
                    ->applyConfig($attributes[1] ?? []);

                if ($className !== HasMany::class) {
                    $rules[$property->getName()] = $nestedRule;
                } else {
                    /**
                     * @psalm-suppress UndefinedMethod
                     */
                    $rules[$property->getName()][] = Each::rule(new RuleSet([$nestedRule]))
                        ->applyConfig($attributes[2] ?? []);
                }
            }

            $useEach = false;
            $eachRuleConfig = [];
            $flatRules = [];
            /**
             * @psalm-suppress UndefinedMethod
             */
            $attributes = $property->getAttributes(Validate::class);
            foreach ($attributes as $index => $attribute) {
                if ($attribute->getArguments()[0] === Each::class) {
                    if ($index !== 0) {
                        throw new InvalidArgumentException('Each is only allowed in the first annotation.');
                    }

                    $useEach = true;
                    $eachRuleConfig = $attribute->getArguments()[1] ?? [];

                    continue;
                }

                $flatRules[] = $attribute->newInstance()->getRule();
            }

            if (!$flatRules) {
                continue;
            }

            $rules[$property->getName()] = $useEach
                ? Each::rule(new RuleSet($flatRules))->applyConfig($eachRuleConfig)
                : $flatRules;
        }

        return $rules;
    }
}
