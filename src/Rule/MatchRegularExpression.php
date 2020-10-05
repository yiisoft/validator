<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\AbstractRule;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\HasValidationErrorMessage;
use Yiisoft\Validator\Result;

/**
 * RegularExpressionValidator validates that the attribute value matches the specified [[pattern]].
 *
 * If the [[not]] property is set true, the validator will ensure the attribute value do NOT match the [[pattern]].
 */
class MatchRegularExpression extends AbstractRule
{
    use HasValidationErrorMessage;

    /**
     * @var string the regular expression to be matched with
     */
    private string $pattern;
    /**
     * @var bool whether to invert the validation logic. Defaults to false. If set to true,
     * the regular expression defined via [[pattern]] should NOT match the attribute value.
     */
    private bool $not = false;

    private string $message = 'Value is invalid.';

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
    }

    protected function validateValue($value, DataSetInterface $dataSet = null): Result
    {
        $result = new Result();

        $valid = !is_array($value) &&
            ((!$this->not && preg_match($this->pattern, $value))
                || ($this->not && !preg_match($this->pattern, $value)));

        if (!$valid) {
            $result->addError($this->translateMessage($this->message));
        }

        return $result;
    }

    public function not(): self
    {
        $new = clone $this;
        $new->not = true;
        return $new;
    }

    public function getName(): string
    {
        return 'matchRegularExpression';
    }

    public function getOptions(): array
    {
        return array_merge(
            parent::getOptions(),
            [
                'message' => $this->translateMessage($this->message),
                'not' => $this->not,
                'pattern' => $this->pattern
            ],
        );
    }
}
