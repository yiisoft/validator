<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Attribute;
use Closure;
use JetBrains\PhpStorm\ArrayShape;
use Yiisoft\Validator\Rule\Trait\RuleNameTrait;
use Yiisoft\Validator\Rule\Trait\HandlerClassNameTrait;
use Yiisoft\Validator\ParametrizedRuleInterface;

/**
 * Validates that the value is a valid json.
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
final class Json implements ParametrizedRuleInterface
{
    use HandlerClassNameTrait;
    use RuleNameTrait;

    public function __construct(
        private string $message = 'The value is not JSON.',
        private bool $skipOnEmpty = false,
        private bool $skipOnError = false,
        private ?Closure $when = null,
    ) {
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isSkipOnEmpty(): bool
    {
        return $this->skipOnEmpty;
    }

    /**
     * @return bool
     */
    public function isSkipOnError(): bool
    {
        return $this->skipOnError;
    }

    /**
     * @return Closure|null
     */
    public function getWhen(): ?Closure
    {
        return $this->when;
    }

    #[ArrayShape(['message' => "string[]", 'skipOnEmpty' => "bool", 'skipOnError' => "bool"])]
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
