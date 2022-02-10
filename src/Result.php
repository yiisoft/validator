<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Closure;
use Yiisoft\Arrays\ArrayHelper;

use function array_slice;
use function implode;

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
    public function getErrorObjects(): array
    {
        return $this->errors;
    }

    /**
     * @return string[]
     */
    public function getErrors(): array
    {
        return ArrayHelper::getColumn($this->errors, static fn (Error $error) => $error->getMessage());
    }

    /**
     * @psalm-return array<string, non-empty-list<string>>
     */
    public function getErrorsIndexedByPath(string $separator = '.'): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $stringValuePath = implode($separator, $error->getValuePath());
            $errors[$stringValuePath][] = $error->getMessage();
        }

        return $errors;
    }

    /**
     * @psalm-return array<int|string, non-empty-list<int|string>>
     */
    public function getErrorsIndexedByAttribute(): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $key = $error->getValuePath()[0] ?? '';
            $errors[$key][] = $error->getMessage();
        }

        return $errors;
    }

    /**
     * @psalm-return array<string, Error[]>
     */
    public function getAttributeErrorObjects(string $attribute): array
    {
        return $this->getAttributeErrorsMap($attribute, static fn (Error $error): Error => $error);
    }

    /**
     * @psalm-return array<string, string[]>
     */
    public function getAttributeErrors(string $attribute): array
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
    public function getAttributeErrorsIndexedByPath(string $attribute, string $separator = '.'): array
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
     * @psalm-param array<int|string> $valuePath
     */
    public function addError(string $message, array $valuePath = []): void
    {
        $this->errors[] = new Error($message, $valuePath);
    }
}
