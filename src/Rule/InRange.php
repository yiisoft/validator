<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\ValidationContext;
use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\HasValidationErrorMessage;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

/**
 * In validates that the attribute value is among a list of values.
 *
 * The range can be specified via constructor.
 * If the {@see InRange::not()} is called, the validator will ensure the attribute value
 * is NOT among the specified range.
 */
class InRange extends Rule
{
    use HasValidationErrorMessage;

    /**
     * @var iterable
     */
    private iterable $range;
    /**
     * @var bool whether the comparison is strict (both type and value must be the same)
     */
    private bool $strict = false;
    /**
     * @var bool whether to invert the validation logic. Defaults to false. If set to true,
     * the attribute value should NOT be among the list of values passed via constructor.
     */
    private bool $not = false;

    private string $message = 'This value is invalid.';

    public function __construct(iterable $range)
    {
        $this->range = $range;
    }

    protected function validateValue($value, ValidationContext $context = null): Result
    {
        $in = false;

        if (!$in && ArrayHelper::isIn($value, $this->range, $this->strict)) {
            $in = true;
        }

        $result = new Result();

        if ($this->not === $in) {
            $result->addError($this->formatMessage($this->message));
        }

        return $result;
    }

    public function strict(): self
    {
        $new = clone $this;
        $new->strict = true;
        return $new;
    }

    public function not(): self
    {
        $new = clone $this;
        $new->not = true;
        return $new;
    }

    public function getOptions(): array
    {
        return array_merge(
            parent::getOptions(),
            [
                'message' => $this->formatMessage($this->message),
                'range' => $this->range,
                'strict' => $this->strict,
                'not' => $this->not,
            ],
        );
    }
}
