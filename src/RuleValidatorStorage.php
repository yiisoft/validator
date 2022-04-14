<?php

declare(strict_types=1);

namespace Yiisoft\Validator;

use RuntimeException;
use Yiisoft\Validator\Rule\AtLeast\AtLeastValidator;
use Yiisoft\Validator\Rule\Boolean\BooleanValidator;
use Yiisoft\Validator\Rule\Callback\CallbackValidator;
use Yiisoft\Validator\Rule\CompareTo\CompareToValidator;
use Yiisoft\Validator\Rule\Count\CountValidator;
use Yiisoft\Validator\Rule\Each\EachValidator;
use Yiisoft\Validator\Rule\Email\EmailValidator;
use Yiisoft\Validator\Rule\GroupRule\GroupRuleValidator;
use Yiisoft\Validator\Rule\HasLength\HasLengthValidator;
use Yiisoft\Validator\Rule\InRange\InRangeValidator;
use Yiisoft\Validator\Rule\Ip\IpValidator;
use Yiisoft\Validator\Rule\Json\JsonValidator;
use Yiisoft\Validator\Rule\Nested\NestedValidator;
use Yiisoft\Validator\Rule\Number\NumberValidator;
use Yiisoft\Validator\Rule\Regex\RegexValidator;
use Yiisoft\Validator\Rule\Required\RequiredValidator;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\Rule\Subset\SubsetValidator;
use Yiisoft\Validator\Rule\Url\UrlValidator;

class RuleValidatorStorage
{
    private array $mapping;

    public function __construct(array $mapping = [])
    {
        $this->mapping = [
            AtLeastValidator::getConfigClassName() => new AtLeastValidator(),
            BooleanValidator::getConfigClassName() => new BooleanValidator(),
            CallbackValidator::getConfigClassName() => new CallbackValidator(),
            CompareToValidator::getConfigClassName() => new CompareToValidator(),
            CountValidator::getConfigClassName() => new CountValidator(),
            EachValidator::getConfigClassName() => new EachValidator(),
            EmailValidator::getConfigClassName() => new EmailValidator(),
            GroupRuleValidator::getConfigClassName() => new GroupRuleValidator(),
            HasLengthValidator::getConfigClassName() => new HasLengthValidator(),
            InRangeValidator::getConfigClassName() => new InRangeValidator(),
            IpValidator::getConfigClassName() => new IpValidator(),
            JsonValidator::getConfigClassName() => new JsonValidator(),
            NumberValidator::getConfigClassName() => new NumberValidator(),
            RegexValidator::getConfigClassName() => new RegexValidator(),
            RequiredValidator::getConfigClassName() => new RequiredValidator(),
            SubsetValidator::getConfigClassName() => new SubsetValidator(),
            UrlValidator::getConfigClassName() => new UrlValidator(),
            NestedValidator::getConfigClassName() => new NestedValidator(),
        ];
    }

    public function getValidator(string $rule): RuleValidatorInterface
    {
        foreach ($this->mapping as $processingClass => $validator) {
            if (is_a($rule, $processingClass, true)) {
                return $validator;
            }
        }

        throw new RuntimeException("No validator found for \"$rule\" rule.");
    }
}
