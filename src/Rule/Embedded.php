<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use ReflectionProperty;
use Yiisoft\Validator\BeforeValidationInterface;
use Yiisoft\Validator\Rule\Trait\BeforeValidationTrait;
use Yiisoft\Validator\SerializableRuleInterface;
use Yiisoft\Validator\ValidationContext;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Embedded implements SerializableRuleInterface, BeforeValidationInterface
{
    use BeforeValidationTrait;

    public function __construct(
        private int $propertyVisibility = ReflectionProperty::IS_PRIVATE | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PUBLIC,
        private bool $skipOnEmpty = false,
        /**
         * @var callable|null
         */
        private $skipOnEmptyCallback = null,
        private bool $skipOnError = false,
        /**
         * @var Closure(mixed, ValidationContext):bool|null
         */
        private ?Closure $when = null,
    ) {
        $this->initSkipOnEmptyProperties($skipOnEmpty, $skipOnEmptyCallback);
    }

    public function getName(): string
    {
        return 'Embedded';
    }

    public function getHandlerClassName(): string
    {
        return EmbeddedHandler::class;
    }

    public function getPropertyVisibility(): int
    {
        return $this->propertyVisibility;
    }

    public function getOptions(): array
    {
        return [
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
