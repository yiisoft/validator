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
     * @psalm-param list<int|string>|null $valuePath
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
        return ArrayHelper::getColumn($this->errors, function ($error) {
            /** @var Error $error */
            return $error->getMessage();
        });
    }

    /**
     * @return array
     */
    public function getNestedDetailedErrors(): array
    {
        $valuePathCountMap = [];
        $errors = [];
        foreach ($this->errors as $error) {
            $stringValuePath = $error->getStringValuePath();
            $valuePathCount = $valuePathCountMap[$stringValuePath] ?? 0;
            $errorValuePath = "$stringValuePath.$valuePathCount";

            ArrayHelper::setValueByPath($errors, $errorValuePath, $error->getMessage());
            $valuePathCount++;
            $valuePathCountMap[$stringValuePath] = $valuePathCount;
        }

        return $errors;
    }

    /**
     * @return array
     */
    public function getFlatDetailedErrors(): array
    {
        $errors = [];
        foreach ($this->errors as $error) {
            $errors[$error->getStringValuePath()][] = $error->getMessage();
        }

        return $errors;
    }
}
