<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class ErrorMessage
{
    private string $message = '';
    private array $parameters = [];

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

    public function getFormattedMessage(): string
    {
        return $this->format($this->message, $this->parameters);
    }

    public function __toString(): string
    {
        return $this->getFormattedMessage();
    }

    private function format(string $message, array $params = []): string
    {
        $replacements = [];
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                $value = 'array';
            } elseif (is_object($value)) {
                $value = 'object';
            } elseif (is_resource($value)) {
                $value = 'resource';
            }
            $replacements['{' . $key . '}'] = $value;
        }
        return strtr($message, $replacements);
    }
}
