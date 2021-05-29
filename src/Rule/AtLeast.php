<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\HasValidationErrorMessage;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

/**
 * AtLeastValidator checks if at least $min of many attributes are filled.
 */
class AtLeast extends Rule
{
    use HasValidationErrorMessage;

    /**
     * The minimum required quantity of filled attributes to pass the validation.
     * Defaults to 1.
     */
    private int $min = 1;

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
     */
    public static function rule(array $attributes): self
    {
        $rule = new self();
        $rule->attributes = $attributes;
        return $rule;
    }

    protected function validateValue($value, ValidationContext $context = null): Result
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
                $this->formatMessage(
                    $this->message,
                    [
                        'min' => $this->min,
                    ]
                )
            );
        }

        return $result;
    }

    /**
     * @param int $value The minimum required quantity of filled attributes to pass the validation.
     *
     * @return self
     */
    public function min(int $value): self
    {
        $new = clone $this;
        $new->min = $value;
        return $new;
    }

    public function getOptions(): array
    {
        return array_merge(
            parent::getOptions(),
            ['min' => $this->min],
            ['message' => $this->formatMessage($this->message, ['min' => $this->min])],
        );
    }
}
