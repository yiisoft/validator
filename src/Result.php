<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

final class Result
{
    /**
     * @var ErrorMessage[]
     */
    private array $errors = [];

    public function isValid(): bool
    {
        return $this->errors === [];
    }

    public function addError(ErrorMessage $error): void
    {
        $this->errors[] = $error;
    }

    /**
     * @return ErrorMessage[]
     */
    public function getErrors(?ErrorMessageFormatterInterface $formatter = null): array
    {
        if ($formatter instanceof ErrorMessageFormatterInterface) {
            return array_map(
                function($error) use ($formatter) {
                    return new ErrorMessage($error->getMessage(), $error->getParameters(), $formatter);
                },
                $this->errors
            );
        }
        return $this->errors;
    }
}
