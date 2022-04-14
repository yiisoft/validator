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
            $firstItem = $error->getAttribute();
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

    public function addError(string $message, array $parameters = [], string $attribute = null): self
    {
        if ($this->attribute !== null) {
            if ($attribute !== null) {
                $attribute = $this->attribute . '.' . $attribute;
            } else {
                $attribute = $this->attribute;
            }
        }
        $this->errors[] = new Error($message, $parameters, $attribute);

        return $this;
    }

    /**
     * @psalm-param array<int|string> $parameters
     */
    public function merge(Error $error): self
    {
        $this->addError(
            $error->getMessage(),
            $error->getParameters(),
            $error->getAttribute(),
        );

        return $this;
    }
}
