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
class RegularExpression extends Rule
{
    /**
     * @var string the regular expression to be matched with
     */
    public $pattern;
    /**
     * @var bool whether to invert the validation logic. Defaults to false. If set to true,
     * the regular expression defined via [[pattern]] should NOT match the attribute value.
     */
    public $not = false;

    public function __construct()
    {
        if ($this->pattern === null) {
            throw new \RuntimeException('The "pattern" property must be set.');
        }
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is invalid.');
        }
    }

    public function validateValue($value): Result
    {
        $result = new Result();

        $valid = !is_array($value) &&
            (!$this->not && preg_match($this->pattern, $value)
            || $this->not && !preg_match($this->pattern, $value));

        if (!$valid) {
            $result->addError($this->message);
        }

        return $result;
    }
}
