<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class ErrorMessage
{
    private string $message = '';
    private array $parameters = [];
    private ErrorMessageFormatterInterface $formatter;

    public function __construct(string $message, array $parameters = [], ?ErrorMessageFormatterInterface $formatter = null)
    {
        $this->message = $message;
        $this->parameters = $parameters;
        $this->formatter = $formatter ?? new ErrorMessageFormatter();
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getFormattedMessage(): string
    {
        return $this->formatter->format($this);
    }

    public function __toString(): string
    {
        return $this->getFormattedMessage();
    }
}
