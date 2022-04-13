<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Regex;

use Attribute;
use Closure;

/**
 * Validates that the value matches the pattern specified in constructor.
 *
 * If the {@see Regex::$not} is used, the rule will ensure the value do NOT match the pattern.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Regex
{
    public function __construct(
        /**
         * @var string the regular expression to be matched with
         */
        public string   $pattern,
        /**
         * @var bool whether to invert the validation logic. Defaults to `false`. If set to `true`, the regular
         * expression defined via {@see $pattern} should NOT match the value.
         */
        public bool     $not = false,
        public string   $incorrectInputMessage = 'Value should be string.',
        public string   $message = 'Value is invalid.',
        public bool     $skipOnEmpty = false,
        public bool     $skipOnError = false,
        public ?Closure $when = null,
    )
    {

    }

    public function getOptions(): array
    {
        return [
            'pattern' => $this->pattern,
            'not' => $this->not,
            'incorrectInputMessage' => [
                'message' => $this->incorrectInputMessage,
            ],
            'message' => [
                'message' => $this->message,
            ],
        ];
    }
}
