<?php
declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

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
use Yiisoft\Validator\ValidatorStorage;

class FakeRuleStorageFactory
{
    public static function make(): ValidatorStorage
    {
        return new ValidatorStorage([
            AtLeastValidator::getRuleClassName() => new AtLeastValidator(),
            BooleanValidator::getRuleClassName() => new BooleanValidator(),
            CallbackValidator::getRuleClassName() => new CallbackValidator(),
            CompareToValidator::getRuleClassName() => new CompareToValidator(),
            CountValidator::getRuleClassName() => new CountValidator(),
            EachValidator::getRuleClassName() => new EachValidator(),
            EmailValidator::getRuleClassName() => new EmailValidator(),
            GroupRuleValidator::getRuleClassName() => new GroupRuleValidator(),
            HasLengthValidator::getRuleClassName() => new HasLengthValidator(),
            InRangeValidator::getRuleClassName() => new InRangeValidator(),
            IpValidator::getRuleClassName() => new IpValidator(),
            JsonValidator::getRuleClassName() => new JsonValidator(),
            NumberValidator::getRuleClassName() => new NumberValidator(),
            RegexValidator::getRuleClassName() => new RegexValidator(),
            RequiredValidator::getRuleClassName() => new RequiredValidator(),
            SubsetValidator::getRuleClassName() => new SubsetValidator(),
            UrlValidator::getRuleClassName() => new UrlValidator(),
            NestedValidator::getRuleClassName() => new NestedValidator(),
        ]);
    }
}
