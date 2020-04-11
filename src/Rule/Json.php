<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\HasValidationMessage;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\DataSetInterface;
use Exception;

/**
 * JsonValidator validates that the attribute value is a valid json
 */
class Json extends Rule
{
    use HasValidationMessage;

    protected string $message = 'The value is not json';

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();

        if (!$this->isValidJson($value)) {
            $result->addError($this->translateMessage($this->message));
        }

        return $result;
    }

    private function isValidJson($value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        try {
            json_decode($value, false, 512, JSON_THROW_ON_ERROR);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
