<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Json;

use Attribute;
use Closure;

/**
 * Validates that the value is a valid json.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Json
{
    public function __construct(
        public string   $message = 'The value is not JSON.',
        public bool     $skipOnEmpty = false,
        public bool     $skipOnError = false,
        public ?Closure $when = null,
    ) {
    }

    public function getOptions(): array
    {
        return [
            'message' => [
                'message' => $this->message,
            ],
            'skipOnEmpty' => $this->skipOnEmpty,
            'skipOnError' => $this->skipOnError,
        ];
    }
}
