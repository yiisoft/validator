<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Stringable;

final class Error
{
    private ErrorMessage $message;

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
    public function __construct(string|Stringable|ErrorMessage $message, array $valuePath = [], array $parameters = [])
    {
        $this->message = $message instanceof ErrorMessage ? $message : new ErrorMessage((string) $message, $parameters);
        $this->valuePath = $valuePath;
        $this->parameters = $parameters;
    }

    public function getMessage(): ErrorMessage
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
            static fn ($key): string => str_replace(['.', '*'], ['\\' . '.', '\\' . '*'], (string)$key),
            $this->valuePath
        );
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
