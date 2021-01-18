<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class Message
{
    private string $message;
    private array $parameters;

    public function __construct(string $message, array $parameters = [])
    {
        $this->message = $message;
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
}
