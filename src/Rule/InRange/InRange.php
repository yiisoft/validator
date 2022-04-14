<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\InRange;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\RuleNameTrait;
use Yiisoft\Validator\RuleInterface;

/**
 * Validates that the value is among a list of values.
 *
 * The range can be specified via constructor.
 * If the {@see InRange::$not} is called, the rule will ensure the value is NOT among the specified range.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class InRange implements RuleInterface
{
    use RuleNameTrait;

    public function __construct(
        public iterable $range,
        /**
         * @var bool whether the comparison is strict (both type and value must be the same)
         */
        public bool $strict = false,
        /**
         * @var bool whether to invert the validation logic. Defaults to false. If set to `true`, the value should NOT
         * be among the list of values passed via constructor.
         */
        public bool $not = false,
        public string $message = 'This value is invalid.',
        public bool $skipOnEmpty = false,
        public bool $skipOnError = false,
        public ?Closure $when = null,
    ) {
    }

    public function getOptions(): array
    {
        return [
            'range' => $this->range,
            'strict' => $this->strict,
            'not' => $this->not,
            'message' => [
                'message' => $this->message,
            ],
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
