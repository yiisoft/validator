<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Validators;

use yii\base\Component;
use yii\di\AbstractContainer;
use yii\exceptions\NotSupportedException;
use yii\helpers\Yii;
use yii\i18n\I18N;

/**
 * Validator is the base class for all validators.
 *
 * Child classes should override the [[validateValue()]] and/or [[validateAttribute()]] methods to provide the actual
 * logic of performing data validation. Child classes may also override [[clientValidateAttribute()]]
 * to provide client-side validation support.
 *
 * Validator declares a set of [[builtInValidators|built-in validators]] which can
 * be referenced using short names. They are listed as follows:
 *
 * - `boolean`: [[BooleanValidator]]
 * - `compare`: [[CompareValidator]]
 * - `date`: [[DateValidator]]
 * - `datetime`: [[DateValidator]]
 * - `time`: [[DateValidator]]
 * - `default`: [[DefaultValueValidator]]
 * - `double`: [[NumberValidator]]
 * - `each`: [[EachValidator]]
 * - `email`: [[EmailValidator]]
 * - `exist`: [[ExistValidator]]
 * - `file`: [[FileValidator]]
 * - `filter`: [[FilterValidator]]
 * - `image`: [[ImageValidator]]
 * - `in`: [[RangeValidator]]
 * - `integer`: [[NumberValidator]]
 * - `match`: [[RegularExpressionValidator]]
 * - `required`: [[RequiredValidator]]
 * - `safe`: [[SafeValidator]]
 * - `string`: [[StringValidator]]
 * - `trim`: [[FilterValidator]]
 * - `unique`: [[UniqueValidator]]
 * - `url`: [[UrlValidator]]
 * - `ip`: [[IpValidator]]
 *
 * For more details and usage information on Validator, see the [guide article on validators](guide:input-validation).
 *
 * @property array $attributeNames Attribute names. This property is read-only.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Rule2
{
    /**
     * @var array list of built-in validators (name => class or configuration)
     */
    public static $builtInValidators = [
        'boolean' => Boolean::class,
        'compare' => Compare::class,
        'date' => Date::class,
        'datetime' => [
            '__class' => Date::class,
            'type' => Date::TYPE_DATETIME,
        ],
        'time' => [
            '__class' => Date::class,
            'type' => Date::TYPE_TIME,
        ],
        'default' => DefaultValue::class,
        'double' => Number::class,
        'each' => Each::class,
        'email' => Email::class,
        'exist' => Exist::class,
        'file' => File::class,
        'filter' => Filter::class,
        'image' => Image::class,
        'in' => Range::class,
        'integer' => [
            '__class' => Number::class,
            'integerOnly' => true,
        ],
        'match' => RegularExpression::class,
        'number' => Number::class,
        'required' => Required::class,
        'safe' => Safe::class,
        'string' => String::class,
        'trim' => [
            '__class' => Filter::class,
            'filter' => 'trim',
            'skipOnArray' => true,
        ],
        'unique' => Unique::class,
        'url' => Url::class,
        'ip' => Ip::class,
    ];

    /**
     * @var string the user-defined error message. It may contain the following placeholders which
     * will be replaced accordingly by the validator:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     *
     * Note that some validators may introduce other properties for error messages used when specific
     * validation conditions are not met. Please refer to individual class API documentation for details
     * about these properties. By convention, this property represents the primary error message
     * used when the most important validation condition is not met.
     */
    public $message;
    /**
     * @var bool whether this validation rule should be skipped if the attribute being validated
     * already has some validation error according to some previous rules. Defaults to true.
     */
    public $skipOnError = true;
    /**
     * @var bool whether this validation rule should be skipped if the attribute value
     * is null or an empty string.
     */
    public $skipOnEmpty = true;
    /**
     * @var bool whether to enable client-side validation for this validator.
     * The actual client-side validation is done via the JavaScript code returned
     * by [[clientValidateAttribute()]]. If that method returns null, even if this property
     * is true, no client-side validation will be done by this validator.
     */
    public $enableClientValidation = true;
    /**
     * @var callable a PHP callable that replaces the default implementation of [[isEmpty()]].
     * If not set, [[isEmpty()]] will be used to check if a value is empty. The signature
     * of the callable should be `function ($value)` which returns a boolean indicating
     * whether the value is empty.
     */
    public $isEmpty;
    /**
     * @var callable a PHP callable whose return value determines whether this validator should be applied.
     * The signature of the callable should be `function ($model, $attribute)`, where `$model` and `$attribute`
     * refer to the model and the attribute currently being validated. The callable should return a boolean value.
     *
     * This property is mainly provided to support conditional validation on the server-side.
     * If this property is not set, this validator will be always applied on the server-side.
     *
     * The following example will enable the validator only when the country currently selected is USA:
     *
     * ```php
     * function ($model) {
     *     return $model->country == Country::USA;
     * }
     * ```
     *
     * @see whenClient
     */
    public $when;
    /**
     * @var string a JavaScript function name whose return value determines whether this validator should be applied
     * on the client-side. The signature of the function should be `function (attribute, value)`, where
     * `attribute` is an object describing the attribute being validated (see [[clientValidateAttribute()]])
     * and `value` the current value of the attribute.
     *
     * This property is mainly provided to support conditional validation on the client-side.
     * If this property is not set, this validator will be always applied on the client-side.
     *
     * The following example will enable the validator only when the country currently selected is USA:
     *
     * ```javascript
     * function (attribute, value) {
     *     return $('#country').val() === 'USA';
     * }
     * ```
     *
     * @see when
     */
    public $whenClient;




    public function init(): void
    {
        $this->attributes = (array) $this->attributes;

    }

    /**
     * Creates a validator object.
     * @param string|\Closure $type the validator type. This can be either:
     *  * a built-in validator name listed in [[builtInValidators]];
     *  * a method name of the model class;
     *  * an anonymous function;
     *  * a validator class name.
     * @param \yii\base\Model $model the data model to be validated.
     * @param array|string $attributes list of attributes to be validated. This can be either an array of
     * the attribute names or a string of comma-separated attribute names.
     * @param array $params initial values to be applied to the validator properties.
     * @return Rule2 the validator
     */
    public static function createValidator($type, $model, $attributes, $params = [])
    {
        $params['attributes'] = $attributes;

        if ($type instanceof \Closure || ($model->hasMethod($type) && !isset(static::$builtInValidators[$type]))) {
            // method-based validator
            $class = __NAMESPACE__ . '\InlineValidator';
            $params['method'] = $type;
        } else {
            if (isset(static::$builtInValidators[$type])) {
                $type = static::$builtInValidators[$type];
            }
            if (is_array($type)) {
                $params = array_merge($type, $params);
                $class = $params['__class'];
            } else {
                $class = $type;
            }
        }

        unset($params['__class']);

        return Yii::createObject($class, [$params]);
    }

    /**
     * Validates the specified object.
     * @param \yii\base\Model $model the data model being validated
     * @param array|null $attributes the list of attributes to be validated.
     * Note that if an attribute is not associated with the validator - it will be
     * ignored. If this parameter is null, every attribute listed in [[attributes]] will be validated.
     */
    public function validateAttributes($model, $attributes = null)
    {
        if (is_array($attributes)) {
            $newAttributes = [];
            $attributeNames = $this->getAttributeNames();
            foreach ($attributes as $attribute) {
                if (in_array($attribute, $attributeNames, true)) {
                    $newAttributes[] = $attribute;
                }
            }
            $attributes = $newAttributes;
        } else {
            $attributes = $this->getAttributeNames();
        }

        foreach ($attributes as $attribute) {
            $skip = $this->skipOnError && $model->hasErrors($attribute)
                || $this->skipOnEmpty && $this->isEmpty($model->$attribute);
            if (!$skip) {
                if ($this->when === null || call_user_func($this->when, $model, $attribute)) {
                    $this->validateAttribute($model, $attribute);
                }
            }
        }
    }



    /**
     * Validates a given value.
     * You may use this method to validate a value out of the context of a data model.
     * @param mixed $value the data value to be validated.
     * @param string $error the error message to be returned, if the validation fails.
     * @return bool whether the data is valid.
     */
    public function validate($value, &$error = null)
    {
        $result = $this->validateValue($value);
        if (empty($result)) {
            return true;
        }

        [$message, $params] = $result;
        $params['attribute'] = Yii::t('yii', 'the input value');
        if (is_array($value)) {
            $params['value'] = 'array()';
        } elseif (is_object($value)) {
            $params['value'] = 'object';
        } else {
            $params['value'] = $value;
        }
        $error = $this->formatMessage($message, $params);

        return false;
    }

    /**
     * Checks if the given value is empty.
     * A value is considered empty if it is null, an empty array, or an empty string.
     * Note that this method is different from PHP empty(). It will return false when the value is 0.
     * @param mixed $value the value to be checked
     * @return bool whether the value is empty
     */
    public function isEmpty($value)
    {
        if ($this->isEmpty !== null) {
            return call_user_func($this->isEmpty, $value);
        }

        return $value === null || $value === [] || $value === '';
    }
}
