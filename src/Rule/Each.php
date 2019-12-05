<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace Yiisoft\Validator\Rule;

use yii\helpers\Yii;
use yii\exceptions\InvalidConfigException;
use yii\base\Model;
use Yiisoft\Validator\DataSetInterface;
use Yiisoft\Validator\Result;
use Yiisoft\Validator\Rule;

/**
 * EachValidator validates an array by checking each of its elements against an embedded validation rule.
 *
 * ```php
 * class MyModel extends Model
 * {
 *     public $categoryIDs = [];
 *
 *     public function rules()
 *     {
 *         return [
 *             // checks if every category ID is an integer
 *             ['categoryIDs', 'each', 'rule' => ['integer']],
 *         ]
 *     }
 * }
 * ```
 *
 * > Note: This validator will not work with inline validation rules in case of usage outside the model scope,
 *   e.g. via [[validateValue()]] method.
 *
 * > Note: EachValidator is meant to be used only in basic cases, you should consider usage of tabular input,
 *   using several models for the more complex case.
 */
class Each extends Rule
{
    /**
     * @var array|Rule definition of the validation rule, which should be used on array values.
     * It should be specified in the same format as at [[\yii\base\Model::rules()]], except it should not
     * contain attribute list as the first element.
     * For example:
     *
     * ```php
     * ['integer']
     * ['match', 'pattern' => '/[a-z]/is']
     * ```
     *
     * Please refer to [[\yii\base\Model::rules()]] for more details.
     */
    private $rule;
    /**
     * @var bool whether to use error message composed by validator declared via [[rule]] if its validation fails.
     * If enabled, error message specified for this validator itself will appear only if attribute value is not an array.
     * If disabled, own error message value will be used always.
     */
    private $allowMessageFromRule = true;
    /**
     * @var bool whether to stop validation once first error among attribute value elements is detected.
     * When enabled validation will produce single error message on attribute, when disabled - multiple
     * error messages mya appear: one per each invalid value.
     * Note that this option will affect only [[validateAttribute()]] value, while [[validateValue()]] will
     * not be affected.
     */
    private $stopOnFirstError = true;

    private $message;

    /**
     * @var Rule validator instance.
     */
    private $_validator;

    public function __construct()
    {
        $this->message = '{attribute} is invalid.';
    }

    /**
     * Returns the validator declared in [[rule]].
     * @param DataSetInterface|null $data model in which context validator should be created.
     * @return Rule the declared validator.
     */
    private function getValidator(DataSetInterface $data = null)
    {
        if ($this->_validator === null) {
            $this->_validator = $this->createEmbeddedValidator($data);
        }

        return $this->_validator;
    }

    /**
     * Creates validator object based on the validation rule specified in [[rule]].
     * @param DataSetInterface|null $data model in which context validator should be created.
     * @return Rule validator instance
     *@throws \yii\exceptions\InvalidConfigException
     */
    private function createEmbeddedValidator(DataSetInterface $data)
    {
        $rule = $this->rule;
        if ($rule instanceof Rule) {
            return $rule;
        }

        if (is_array($rule) && isset($rule[0])) { // validator type
            if (!is_object($data)) {
                $data = new Model(); // mock up context model
            }

            return Rule::createValidator($rule[0], $data, $this->attributes, array_slice($rule, 1));
        }

        throw new InvalidConfigException('Invalid validation rule: a rule must be an array specifying validator type.');
    }

    public function validateAttribute(DataSetInterface $data, string $attribute): Result
    {
        $result = new Result();
        $value = $data->getValue($attribute);
        if (!is_array($value) && !$value instanceof \ArrayAccess) {
            $result->addError($this->formatMessage($this->message));
            return $result;
        }

        $validator = $this->getValidator($data); // ensure model context while validator creation

        $detectedErrors = $result->getErrors();

        foreach ($value as $k => $v) {
            $model->clearErrors($attribute);
            $model->$attribute = $v;
            if (!$validator->skipOnEmpty || !$validator->isEmpty($v)) {
                $validator->validateAttribute($model, $attribute);
            }
            $filteredValue[$k] = $model->$attribute;
            if ($model->hasErrors($attribute)) {
                if ($this->allowMessageFromRule) {
                    $validationErrors = $model->getErrors($attribute);
                    $detectedErrors = array_merge($detectedErrors, $validationErrors);
                } else {
                    $model->clearErrors($attribute);
                    $result->addError($this->formatMessage($this->message, ['value' => $v]));
                    $detectedErrors[] = $model->getFirstError($attribute);
                }
                $model->$attribute = $value;

                if ($this->stopOnFirstError) {
                    break;
                }
            }
        }


        $model->clearErrors($attribute);
        $model->addErrors([$attribute => $detectedErrors]);
    }

    public function validateValue($value): Result
    {
        $result = new Result();
        if (!is_array($value) && !$value instanceof \ArrayAccess) {
            $result->addError($this->formatMessage($this->message));
        }

        $validator = $this->getValidator();
        foreach ($value as $v) {
            if ($validator->shouldSkipOnEmpty() && $validator->isEmpty($v)) {
                continue;
            }
            $result = $validator->validateValue($v);
            if ($result !== null) {
                if ($this->allowMessageFromRule) {
                    $result[1]['value'] = $v;
                    return $result;
                }

                return [$this->message, ['value' => $v]];
            }
        }

        return $result;
    }
}
