<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\AtLeast;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\RuleNameTrait;
use Yiisoft\Validator\Rule\HandlerClassNameTrait;
use Yiisoft\Validator\RuleInterface;

/**
 * Checks if at least {@see AtLeast::$min} of many attributes are filled.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class AtLeast implements RuleInterface
{
    use RuleNameTrait;
    use HandlerClassNameTrait;

    public function __construct(
        /**
         * The list of required attributes that will be checked.
         */
        public array $attributes,
        /**
         * The minimum required quantity of filled attributes to pass the validation.
         * Defaults to 1.
         */
        public int $min = 1,
        /**
         * Message to display in case of error.
         */
        public string $message = 'The model is not valid. Must have at least "{min}" filled attributes.',
        public bool $skipOnEmpty = false,
        public bool $skipOnError = false,
        public ?Closure $when = null,
    ) {
    }

    public function getOptions(): array
    {
        return [
            'attributes' => $this->attributes,
            'min' => $this->min,
            'message' => [
                'message' => $this->message,
                'parameters' => ['min' => $this->min],
            ],
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
