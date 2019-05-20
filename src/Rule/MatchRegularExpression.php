<?php

namespace Yiisoft\Validator\Rule;

use yii\helpers\Yii;
use yii\exceptions\InvalidConfigException;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

/**
 * RegularExpressionValidator validates that the attribute value matches the specified [[pattern]].
 *
 * If the [[not]] property is set true, the validator will ensure the attribute value do NOT match the [[pattern]].
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class MatchRegularExpression extends Rule
{
    /**
     * @var string the regular expression to be matched with
     */
    private $pattern;
    /**
     * @var bool whether to invert the validation logic. Defaults to false. If set to true,
     * the regular expression defined via [[pattern]] should NOT match the attribute value.
     */
    private $not = false;

    private $message;

    public function __construct(string $pattern)
    {
        $this->pattern = $pattern;
        $this->message = $this->formatMessage('{attribute} is invalid.');
    }

    public function not(): self
    {
        $this->not = true;
        return $this;
    }

    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    protected function validateValue($value): Result
    {
        $result = new Result();

        $valid = !is_array($value) &&
            ((!$this->not && preg_match($this->pattern, $value))
            || ($this->not && !preg_match($this->pattern, $value)));

        if (!$valid) {
            $result->addError($this->message);
        }

        return $result;
    }
}
