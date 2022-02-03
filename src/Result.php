<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use Yiisoft\Arrays\ArrayHelper;

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

    /**
     * @psalm-param list<int|string> $valuePath
     */
    public function addError(string $message, array $valuePath = []): void
    {
        $this->errors[] = new Error($message, $valuePath);
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

    public function getNestedErrors(string $separator = '.'): array
    {
        $valuePathCountMap = [];
        $errors = [];
        foreach ($this->errors as $error) {
            $stringValuePath = implode($separator, $error->getValuePath());
            $valuePathCount = $valuePathCountMap[$stringValuePath] ?? 0;
            $errorValuePath = "$stringValuePath.$valuePathCount";

            ArrayHelper::setValueByPath($errors, $errorValuePath, $error->getMessage());
            $valuePathCount++;
            $valuePathCountMap[$stringValuePath] = $valuePathCount;
        }

        return $errors;
    }

    public function getErrorsIndexedByPath(string $separator = '.'): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $stringValuePath = implode($separator, $error->getValuePath());
            $errors[$stringValuePath][] = $error->getMessage();
        }

        return $errors;
    }
}
