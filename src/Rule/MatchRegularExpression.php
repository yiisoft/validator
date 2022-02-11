<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Rule;

use Yiisoft\Validator\HasValidationErrorMessage;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\ValidationContext;

use function is_string;

/**
 * RegularExpressionValidator validates that the attribute value matches the pattern specified in constructor.
 *
 * If the {@see MatchRegularExpression::not()} is used, the validator will ensure the attribute value do NOT match
 * the pattern.
 */
final class MatchRegularExpression extends Rule
{
    use HasValidationErrorMessage;

    /**
     * @var string the regular expression to be matched with
     */
    private string $pattern;
    /**
     * @var bool whether to invert the validation logic. Defaults to false. If set to true,
     * the regular expression defined via {@see pattern} should NOT match the attribute value.
     */
    private bool $not = false;

    private string $incorrectInputMessage = 'Value should be string.';
    private string $message = 'Value is invalid.';

    /**
     * @param string $pattern The regular expression to be matched with.
     */
    public static function rule(string $pattern): self
    {
        $rule = new self();
        $rule->pattern = $pattern;
        return $rule;
    }

    protected function validateValue($value, ValidationContext $context = null): Result
    {
        $result = new Result();

        if (!is_string($value)) {
            $result->addError($this->formatMessage($this->incorrectInputMessage));

            return $result;
        }

        if (
            (!$this->not && !preg_match($this->pattern, $value)) ||
            ($this->not && preg_match($this->pattern, $value))
        ) {
            $result->addError($this->formatMessage($this->message));
        }

        return $result;
    }

    public function not(): self
    {
        $new = clone $this;
        $new->not = true;
        return $new;
    }

    public function incorrectInputMessage(string $message): self
    {
        $new = clone $this;
        $new->incorrectInputMessage = $message;
        return $new;
    }

    public function getOptions(): array
    {
        return array_merge(
            parent::getOptions(),
            [
                'message' => $this->formatMessage($this->message),
                'incorrectInputMessage' => $this->formatMessage($this->incorrectInputMessage),
                'not' => $this->not,
                'pattern' => $this->pattern,
            ],
        );
    }
}
