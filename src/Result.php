<?php


namespace Yiisoft\Validator;

/**
 * Result represents validation result
 */
final class Result
{
    private $valid = true;
    private $errors = [];
    
    public function __construct(iterable $messages = [])
    {
        if (!empty($messages)) {
            foreach($messages as $error) {
                $this->addError($error);
            }
        }
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function addError(string $message): void
    {
        $this->valid = false;
        $this->errors[] = $message;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
