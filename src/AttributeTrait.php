<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use ReflectionClass;

trait AttributeTrait
{
    public function rules(): array
    {
        $class = new ReflectionClass($this);
        $rules = [];

        foreach ($class->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $attributes = $property->getAttributes(Validate::class);
            foreach ($attributes as $attribute) {
                $rules[$property->getName()][] = $attribute->newInstance()->getRule();
            }
        }

        return $rules;
    }
}
