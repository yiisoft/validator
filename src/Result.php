<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class Result
{
    /**
     * @var Error[]
     */
    private array $errors = [];
    private ?string $attribute;

    public function __construct(string $attribute = null)
    {
        $this->attribute = $attribute;
    }

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    public function isAttributeValid(string $attribute): bool
    {
        foreach ($this->errors as $error) {
            $firstItem = $error->getParameters()[0] ?? '';
            if ($firstItem === $attribute) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return Error[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @psalm-param array<int|string> $parameters
     */
    public function addError(string $message, array $parameters = [], string $attribute = null): self
    {
        if ($this->attribute !== null) {
            $attribute = $this->attribute . '.' . $attribute;
        }
        $this->errors[] = new Error($message, $parameters, $attribute);

        return $this;
    }
}
