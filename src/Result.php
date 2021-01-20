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
     * @param ErrorMessageFormatterInterface|null $formatter
     *
     * @return ErrorMessage[]
     */
    public function getRawErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param ErrorMessageFormatterInterface|null $formatter
     *
     * @return string[]
     */
    public function getErrors(?ErrorMessageFormatterInterface $formatter = null): array
    {
        return array_map(
            function ($error) use ($formatter) {
                return $error->getFormattedMessage($formatter);
            },
            $this->errors
        );
    }
}
