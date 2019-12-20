<?php
namespace Yiisoft\Validator\Rule;

use Yiisoft\Arrays\ArrayHelper;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

/**
 * In validates that the attribute value is among a list of values.
 *
 * The range can be specified via the [[range]] property.
 * If the [[not]] property is set true, the validator will ensure the attribute value
 * is NOT among the specified range.
 *
 */
class InRange extends Rule
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

    public function __construct($range)
    {
        if (!is_array($range)
            && !($range instanceof \Closure)
            && !($range instanceof \Traversable)
        ) {
            throw new \RuntimeException('The "range" property must be set.');
        }

        $this->range = $range;
        $this->message = $this->formatMessage('{attribute} is invalid.');
    }

    public function strict(): self
    {
        $this->strict = true;
        return $this;
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

    public function allowArray(bool $value): self
    {
        // TODO: do we really need this option?
        $this->allowArray = $value;
        return $this;
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

        if (!$in && static::isIn($value, $this->range, $this->strict)) {
            $in = true;
        }

        $result = new Result();

        if ($this->not === $in) {
            $result->addError($this->message);
        }

        return $result;
    }

    /**
     * Check whether an array or [[\Traversable]] contains an element.
     *
     * This method does the same as the PHP function [in_array()](https://secure.php.net/manual/en/function.in-array.php)
     * but additionally works for objects that implement the [[\Traversable]] interface.
     * @param mixed $needle The value to look for.
     * @param array|\Traversable $haystack The set of values to search.
     * @param bool $strict Whether to enable strict (`===`) comparison.
     * @return bool `true` if `$needle` was found in `$haystack`, `false` otherwise.
     * @throws InvalidArgumentException if `$haystack` is neither traversable nor an array.
     * @see https://secure.php.net/manual/en/function.in-array.php
     * @since 2.0.7
     */
    public static function isIn($needle, $haystack, $strict = false)
    {
        if ($haystack instanceof \Traversable) {
            foreach ($haystack as $value) {
                if ($needle == $value && (!$strict || $needle === $value)) {
                    return true;
                }
            }
        } elseif (is_array($haystack)) {
            return in_array($needle, $haystack, $strict);
        } else {
            throw new InvalidArgumentException('Argument $haystack must be an array or implement Traversable');
        }

        return false;
    }
}
