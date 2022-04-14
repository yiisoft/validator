<?php

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
use Yiisoft\Validator\Rule\Subset\SubsetValidator;
use Yiisoft\Validator\Rule\Url\UrlValidator;

return [
    'yiisoft/validator' => [
        'validators' => [
            AtLeastValidator::class,
            BooleanValidator::class,
            CallbackValidator::class,
            CompareToValidator::class,
            CountValidator::class,
            EachValidator::class,
            EmailValidator::class,
            GroupRuleValidator::class,
            HasLengthValidator::class,
            InRangeValidator::class,
            IpValidator::class,
            JsonValidator::class,
            NumberValidator::class,
            RegexValidator::class,
            RequiredValidator::class,
            SubsetValidator::class,
            UrlValidator::class,
            NestedValidator::class,
        ],
    ],
];
