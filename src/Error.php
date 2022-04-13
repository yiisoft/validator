<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class Error
{
    /**
     * @psalm-param list<int|string> $valuePath
     */
    public function __construct(
        private string $message,
        private array $valuePath = [],
        private array $parameters = [],
        private ?FormatterInterface $formatter = null
    ) {
    }

    public function getFormatter(): ?FormatterInterface
    {
        return $this->formatter;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @psalm-return list<int|string>
     */
    public function getValuePath(): array
    {
        return $this->valuePath;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
