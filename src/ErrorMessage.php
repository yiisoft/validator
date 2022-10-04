<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Stringable;

final class ErrorMessage implements Stringable
{
    private string $message;
    /**
     * @psalm-var array<string, string|integer>
     */
    private array $parameters;

    public function __construct(string|Stringable $message, array $parameters)
    {
        $this->message = (string) $message;
        $this->parameters = $parameters;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function __toString(): string
    {
        return $this->message;
    }
}
