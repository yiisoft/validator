<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\Trait\SkipOnEmptyTrait;
use Yiisoft\Validator\Rule\Trait\SkipOnErrorTrait;
use Yiisoft\Validator\Rule\Trait\WhenTrait;
use Yiisoft\Validator\RuleWithOptionsInterface;
use Yiisoft\Validator\SkipOnEmptyInterface;
use Yiisoft\Validator\SkipOnErrorInterface;
use Yiisoft\Validator\WhenInterface;

/**
 * Validates that the value is one of the values provided in {@see $values}.
 * If the {@see In::$not} is set, the validation logic is inverted and the rule will ensure that the value is NOT one of
 * them.
 *
 * @psalm-import-type WhenType from WhenInterface
 */
#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class In implements RuleWithOptionsInterface, SkipOnErrorInterface, WhenInterface, SkipOnEmptyInterface
{
    use SkipOnEmptyTrait;
    use SkipOnErrorTrait;
    use WhenTrait;

    public function __construct(
        /**
         * @var iterable<scalar>
         */
        private iterable $values,
        /**
         * @var bool Whether the comparison is strict (both type and value must be the same)
         */
        private bool $strict = false,
        /**
         * @var bool Whether to invert the validation logic. Defaults to `false`. If set to `true`, the value must NOT
         * be among the list of {@see $values}.
         */
        private bool $not = false,
        private string $message = 'This value is invalid.',

        /**
         * @var bool|callable|null
         */
        private $skipOnEmpty = null,
        private bool $skipOnError = false,
        /**
         * @var WhenType
         */
        private Closure|null $when = null,
    ) {
    }

    public function getName(): string
    {
        return 'inRange';
    }

    public function getValues(): iterable
    {
        return $this->values;
    }

    public function isStrict(): bool
    {
        return $this->strict;
    }

    public function isNot(): bool
    {
        return $this->not;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getOptions(): array
    {
        return [
            'values' => $this->values,
            'strict' => $this->strict,
            'not' => $this->not,
            'message' => [
                'template' => $this->message,
                'parameters' => [],
            ],
            'skipOnEmpty' => $this->getSkipOnEmptyOption(),
            'skipOnError' => $this->skipOnError,
        ];
    }

    public function getHandler(): string
    {
        return InHandler::class;
    }
}
