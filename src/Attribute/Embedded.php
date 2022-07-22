<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Attribute;

use Attribute;
use Closure;
use ReflectionAttribute;
use ReflectionClass;
use Yiisoft\Validator\Rule\Composite;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Collects all attributes from the reference and represents it as its own.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Embedded extends Composite
{
    /**
     * @psalm-param Closure(mixed, ValidationContext):bool|null $when
     */
    public function __construct(
        private string $referenceClassName,
        bool $skipOnEmpty = false,
        bool $skipOnError = false,
        ?Closure $when = null,
    ) {
        parent::__construct([], $skipOnEmpty, $skipOnError, $when);
    }

    public function getRules(): array
    {
        $classMeta = new ReflectionClass($this->referenceClassName);

        return $this->collectAttributes($classMeta);
    }

    // TODO: use Generator to collect attributes
    private function collectAttributes(ReflectionClass $classMeta): array
    {
        $rules = [];
        foreach ($classMeta->getProperties() as $property) {
            $attributes = $property->getAttributes(RuleInterface::class, ReflectionAttribute::IS_INSTANCEOF);
            foreach ($attributes as $attribute) {
                $rules[$property->getName()][] = $attribute->newInstance();
            }
        }

        return $rules;
    }
}
