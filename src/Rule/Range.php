<?php
namespace Yiisoft\Validator\Rule;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\DataSet;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

/**
 * RangeValidator validates that the attribute value is among a list of values.
 *
 * The range can be specified via the [[range]] property.
 * If the [[not]] property is set true, the validator will ensure the attribute value
 * is NOT among the specified range.
 *
 */
class Range extends Rule
{
    /**
     * @var array|\Traversable|\Closure a list of valid values that the attribute value should be among or an anonymous function that returns
     * such a list. The signature of the anonymous function should be as follows,
     *
     * ```php
     * function($model, $attribute) {
     *     // compute range
     *     return $range;
     * }
     * ```
     */
    private $range;
    /**
     * @var bool whether the comparison is strict (both type and value must be the same)
     */
    private $strict = false;
    /**
     * @var bool whether to invert the validation logic. Defaults to false. If set to true,
     * the attribute value should NOT be among the list of values defined via [[range]].
     */
    private $not = false;
    /**
     * @var bool whether to allow array type attribute.
     */
    private $allowArray = false;

    private $message;

    public function __construct()
    {
        if (!is_array($this->range)
            && !($this->range instanceof \Closure)
            && !($this->range instanceof \Traversable)
        ) {
            throw new \RuntimeException('The "range" property must be set.');
        }
        if ($this->message === null) {
            $this->message = Yii::t('yii', '{attribute} is invalid.');
        }
    }

    public function validateValue($value): Result
    {
        $in = false;

        if ($this->allowArray
            && ($value instanceof \Traversable || is_array($value))
            && ArrayHelper::isSubset($value, $this->range, $this->strict)
        ) {
            $in = true;
        }

        if (!$in && ArrayHelper::isIn($value, $this->range, $this->strict)) {
            $in = true;
        }

        $result = new Result();

        if ($this->not === $in) {
            $result->addError($this->message);
        }

        return $result;
    }

    public function validateAttribute(DataSet $data, string $attribute): Result
    {
        if ($this->range instanceof \Closure) {
            $this->range = call_user_func($this->range, $data, $attribute);
        }
        return parent::validateAttribute($data, $attribute);
    }
}
