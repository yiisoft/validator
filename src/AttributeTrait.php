<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use ReflectionClass;
use Yiisoft\Validator\Attribute\HasMany;
use Yiisoft\Validator\Attribute\HasOne;
use Yiisoft\Validator\Attribute\Validate;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Nested;

trait AttributeTrait
{
    public function getRule(): Rule
    {
        $class = new ReflectionClass($this);

        return $this->handleClass($class);
    }

    private function handleClass(ReflectionClass $class): Rule
    {
        $rules = [];
        foreach ($class->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $attributes = $property->getAttributes(HasMany::class);
            if ($attributes) {
                $relatedClass = new ReflectionClass(new ($attributes[0]->getArguments()[0]));
                $rules[$property->getName()] = Each::rule(new Rules([$this->handleClass($relatedClass)]));
            }

            $attributes = $property->getAttributes(HasOne::class);
            if ($attributes) {
                $relatedClass = new ReflectionClass(new ($attributes[0]->getArguments()[0]));
                $rules[$property->getName()] = $this->handleClass($relatedClass);
            }

            $useEach = false;
            $flatRules = [];
            $attributes = $property->getAttributes(Validate::class);

            foreach ($attributes as $index => $attribute) {
                if ($index ===0 && $attribute->getArguments()[0] === Each::class) {
                    $useEach = true;

                    continue;
                }

                $flatRules[] = $attribute->newInstance()->getRule();
            }

            if (!$flatRules) {
                continue;
            }

            if (!$useEach) {
                $rules[$property->getName()] = $flatRules;
            } else {
                $rules[$property->getName()] = Each::rule(new Rules($flatRules));
            }
        }

        return Nested::rule($rules)->skipOnError(false);
    }
}
