<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class Error
{
    public function __construct(
        private string $message,
        /**
         * @var array<string, scalar|null>
         */
        private array $parameters = [],
        /**
         * @var list<int|string>
         */
        private array $valuePath = [],
    ) {
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array<string, scalar|null>
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return list<int|string>
     */
    public function getValuePath(bool $escape = false): array
    {
        if ($escape === false) {
            return $this->valuePath;
        }

        return array_map(
            static fn ($key): string => str_replace(['.', '*'], ['\\' . '.', '\\' . '*'], (string) $key),
            $this->valuePath
        );
    }
}
