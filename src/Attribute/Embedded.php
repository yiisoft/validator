<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Attribute;

use Attribute;
use ReflectionAttribute;
use ReflectionClass;
use Yiisoft\Validator\Rule\GroupRule;
use Yiisoft\Validator\RuleInterface;

/**
 * Represents one-to-one relation.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Embedded extends GroupRule
{
    public function __construct(private string $relatedClassName)
    {
        parent::__construct();
    }

    public function getRuleSet(): array
    {
        $classMeta = new ReflectionClass($this->relatedClassName);

        return $this->collectAttributes($classMeta);
    }

    // TODO: use Generator to collect attributes
    private function collectAttributes(ReflectionClass $classMeta): array
    {
        $rules = [];
        foreach ($classMeta->getProperties() as $property) {
            $attributes = $property->getAttributes(RuleInterface::class, ReflectionAttribute::IS_INSTANCEOF);
            foreach ($attributes as $attribute) {
                /** @psalm-suppress UndefinedMethod */
                $rules[$property->getName()][] = $attribute->newInstance();
            }
        }

        return $rules;
    }
}
