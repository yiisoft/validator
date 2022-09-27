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
        /**
         * @psalm-var list<int|string>
         */
        private array $valuePath = []
    )
    {
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
            static fn($key): string => str_replace(['.', '*'], ['\\' . '.', '\\' . '*'], (string)$key),
            $this->valuePath
        );
    }
}
