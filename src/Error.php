<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class Error
{
    private string $message;

    /**
     * @psalm-var list<int|string>
     */
    private array $parameters;
    private ?string $attribute;

    /**
     * @psalm-param list<int|mixed> $parameters
     */
    public function __construct(string $message, array $parameters, ?string $attribute = null)
    {
        $this->message = $message;
        $this->parameters = $parameters;
        $this->attribute = $attribute;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @psalm-return list<int|string>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getAttribute(): ?string
    {
        return $this->attribute;
    }
}
