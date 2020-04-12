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
    private int $min;

    /**
     * The list of required attributes that will be checked.
     */
    private array $attributes;

    /**
     * Message to display in case of error.
     */
    private string $message = 'The model is not valid. Must have at least "{min}" filled attributes.';

    /**
     * @param array $attributes The list of required attributes that will be checked.
     * @param int $min The minimum required quantity of filled attributes to pass the validation. Defaults to 1.
     */
    public function __construct(array $attributes, int $min = 1)
    {
        $this->attributes = $attributes;
        $this->min = $min;
    }

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $filledCount = 0;

        foreach ($this->attributes as $attribute) {
            if (!$this->isEmpty($value->{$attribute})) {
                $filledCount++;
            }
        }

        $result = new Result();

        if ($filledCount < $this->min) {
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

    public function message(string $message): self
    {
        $new = clone $this;
        $new->message = $message;
        return $new;
    }
}
