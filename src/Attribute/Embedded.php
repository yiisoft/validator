<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Attribute;

use Attribute;
use Closure;
use ReflectionAttribute;
use ReflectionClass;
use Yiisoft\Validator\Rule\GroupRule;
use Yiisoft\Validator\RuleInterface;
use Yiisoft\Validator\ValidationContext;

/**
 * Collects all attributes from the reference and represents it as its own.
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Embedded extends GroupRule
{
    /**
     * @psalm-param Closure(mixed, ValidationContext):bool|null $when
     */
    public function __construct(
        private string $referenceClassName,
        string $message = 'This value is not a valid.',
        bool $skipOnEmpty = false,
        bool $skipOnError = false,
        ?Closure $when = null,
    ) {
        parent::__construct($message, $skipOnEmpty, $skipOnError, $when);
    }

    public function getRuleSet(): array
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
                /** @psalm-suppress UndefinedMethod */
                $rules[$property->getName()][] = $attribute->newInstance();
            }
        }

        return $rules;
    }
}
