<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Integer extends AbstractNumber
{
    public function __construct(
        float|int|null $min = null,
        float|int|null $max = null,
        string $incorrectInputMessage = 'The allowed types are integer, float and string.',
        string $notNumberMessage = 'Value must be an integer.',
        string $tooSmallMessage = 'Value must be no less than {min}.',
        string $tooBigMessage = 'Value must be no greater than {max}.',
        string $pattern = '/^\s*[+-]?\d+\s*$/',
        mixed $skipOnEmpty = null,
        bool $skipOnError = false,
        Closure|null $when = null,
    ) {
        parent::__construct(
            min: $min,
            max: $max,
            incorrectInputMessage: $incorrectInputMessage,
            notNumberMessage: $notNumberMessage,
            tooSmallMessage: $tooSmallMessage,
            tooBigMessage: $tooBigMessage,
            pattern: $pattern,
            skipOnEmpty: $skipOnEmpty,
            skipOnError: $skipOnError,
            when: $when,
        );
    }

    public function getName(): string
    {
        return 'integer';
    }
}
