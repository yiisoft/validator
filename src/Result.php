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
        return ArrayHelper::getColumn($this->errors, function ($error) {
            /** @var Error $error */
            return $error->getMessage();
        });
    }

    public function getNestedErrors(): array
    {
        $nestedErrors = [];
        foreach ($this->errors as $error) {
            $valuePath = $error->getValuePath();
            if ($valuePath === []) {
                $nestedErrors[0][] = $error->getMessage();
            } else {
                $errors = ArrayHelper::getValue($nestedErrors, $valuePath, []);
                $errors[] = $error->getMessage();

                ArrayHelper::setValue($nestedErrors, $valuePath, $errors);
            }
        }

        return $nestedErrors;
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
