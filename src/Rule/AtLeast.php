<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\DataSetInterface;

/**
 * AtLeastValidator checks if at least $min of many attributes are filled.
 */
class AtLeast extends Rule
{
    /**
     * The minimum required quantity of filled attributes to pass the validation.
     * Defaults to 1.
     */
    private int $min = 1;

    /**
     * The list of required attributes that will be checked.
     */
    private array $attributes = [];

    /**
     * The list of alternative required attributes that will be checked.
     */
    private array $alternativeAttributes = [];

    /**
     * Message to display in case of error
     */
    private string $message = 'The model is not valid. Must have at least "{min}" filled attributes.';

    public function __construct(array $data)
    {
        foreach ($data as $name => $value) {
            $this->{$name} = $value;
        }

        if (empty($this->alternativeAttributes)) {
            $this->alternativeAttributes = $this->attributes;
        } else {
            $this->alternativeAttributes = array_merge($this->attributes, $this->alternativeAttributes);
        }
    }

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $valid = false;
        $filledCount = 0;

        foreach ($this->alternativeAttributes as $attribute) {
            $filledCount += $this->isEmpty($value->{$attribute}) ? 0 : 1;
        }

        if ($filledCount >= $this->min) {
            $valid = true;
        }

        $result = new Result();

        if (!$valid) {
            $result->addError(
                $this->translateMessage(
                    $this->message,
                    [
                        'min' => $this->min,
                    ]
                )
            );
        }

        return $result;
    }
}
