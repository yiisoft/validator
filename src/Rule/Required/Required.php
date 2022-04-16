<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule\Required;

use Attribute;
use Closure;
use Yiisoft\Validator\Rule\RuleNameTrait;
use Yiisoft\Validator\Rule\HandlerClassNameTrait;
use Yiisoft\Validator\RuleInterface;

/**
 * Validates that the specified value is neither null nor empty.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Required implements RuleInterface
{
    use HandlerClassNameTrait;
    use RuleNameTrait;

    public function __construct(
        public string $message = 'Value cannot be blank.',
        public bool $skipOnEmpty = false,
        public bool $skipOnError = false,
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
