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

    public function addResult(self $result): void
    {
        $this->errors = array_merge($this->errors, $result->errors);
    }

    public function addResultWithErrorMessageWrapper(self $result, ErrorMessage $errorMessage): void
    {
        foreach ($result->errors as $error) {
            $this->errors[] = new ErrorMessage(
                $errorMessage->getMessage(),
                array_merge(['error' => $error], $errorMessage->getParameters())
            );
        }
    }

    /**
     * @param ErrorMessageFormatterInterface|null $formatter
     *
     * @return string[]
     */
    public function getErrors(?ErrorMessageFormatterInterface $formatter = null): array
    {
        return array_map(
            static function ($error) use ($formatter) {
                return $error->getFormattedMessage($formatter);
            },
            $this->errors
        );
    }
}
