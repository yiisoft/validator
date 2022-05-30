<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Closure;
use InvalidArgumentException;
use Yiisoft\Arrays\ArrayHelper;

use function array_slice;
use function implode;
use function is_string;

final class Result
{
    /**
     * @var Error[]
     */
    private array $errors = [];

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    public function isAttributeValid(string $attribute): bool
    {
        foreach ($this->errors as $error) {
            $firstItem = $error->getValuePath()[0] ?? '';
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
     * @return string[]
     */
    public function getErrorMessages(): array
    {
        return ArrayHelper::getColumn($this->errors, static fn (Error $error) => $error->getMessage());
    }

    /**
     * @psalm-return array<string, non-empty-list<string>>
     */
    public function getErrorMessagesIndexedByPath(string $separator = '.'): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $stringValuePath = implode($separator, $error->getValuePath());
            $errors[$stringValuePath][] = $error->getMessage();
        }

        return $errors;
    }

    /**
     * @psalm-return array<string, non-empty-list<string>>
     *
     * @throws InvalidArgumentException
     */
    public function getErrorMessagesIndexedByAttribute(): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $key = $error->getValuePath()[0] ?? '';
            if (!is_string($key)) {
                throw new InvalidArgumentException('Top level attributes can only have string type.');
            }

            $errors[$key][] = $error->getMessage();
        }

        return $errors;
    }

    /**
     * @return Error[]
     */
    public function getAttributeErrors(string $attribute): array
    {
        return $this->getAttributeErrorsMap($attribute, static fn (Error $error): Error => $error);
    }

    /**
     * @return string[]
     */
    public function getAttributeErrorMessages(string $attribute): array
    {
        return $this->getAttributeErrorsMap($attribute, static fn (Error $error): string => $error->getMessage());
    }

    private function getAttributeErrorsMap(string $attribute, Closure $getErrorClosure): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $firstItem = $error->getValuePath()[0] ?? '';
            if ($firstItem === $attribute) {
                $errors[] = $getErrorClosure($error);
            }
        }

        return $errors;
    }

    /**
     * @psalm-return array<string, non-empty-list<string>>
     */
    public function getAttributeErrorMessagesIndexedByPath(string $attribute, string $separator = '.'): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $firstItem = $error->getValuePath()[0] ?? '';
            if ($firstItem !== $attribute) {
                continue;
            }

            $valuePath = implode($separator, array_slice($error->getValuePath(), 1));
            $errors[$valuePath][] = $error->getMessage();
        }

        return $errors;
    }

    /**
     * @return string[]
     */
    public function getCommonErrorMessages(): array
    {
        return $this->getAttributeErrorMessages('');
    }

    /**
     * @psalm-param array<scalar> $valuePath
     */
    public function addError(string $message, array $valuePath = []): self
    {
        $this->errors[] = new Error($message, $valuePath);

        return $this;
    }
}
