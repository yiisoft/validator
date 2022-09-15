<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class Error
{
    private string $message;

    /**
     * @psalm-var list<int|string>
     */
    private array $valuePath;
    /**
     * @psalm-var list<int|string>
     */
    private array $parameters;

    /**
     * @psalm-param list<int|string> $valuePath
     */
    public function __construct(string $message, array $valuePath = [], array $parameters = [])
    {
        $this->message = $message;
        $this->valuePath = $valuePath;
        $this->parameters = $parameters;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @psalm-return list<int|string>
     */
    public function getValuePath(bool $escape = false): array
    {
        if ($escape === false) {
            return $this->valuePath;
        }

        return array_map(
            static function ($key): string {
                return str_replace(['.', '*'], ['\\' . '.', '\\' . '*'], (string)$key);
            },
            $this->valuePath
        );
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
