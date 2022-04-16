<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Stub;

use Yiisoft\Test\Support\Container\SimpleContainer;
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
use Yiisoft\Validator\Validator;

final class FakeValidatorFactory
{
    public static function make(): Validator
    {
        return new Validator(
            new SimpleContainer([
                AtLeastValidator::class => new AtLeastValidator(),
                BooleanValidator::class => new BooleanValidator(),
                CallbackValidator::class => new CallbackValidator(),
                CompareToValidator::class => new CompareToValidator(),
                CountValidator::class => new CountValidator(),
                EachValidator::class => new EachValidator(),
                EmailValidator::class => new EmailValidator(),
                GroupRuleValidator::class => new GroupRuleValidator(),
                HasLengthValidator::class => new HasLengthValidator(),
                InRangeValidator::class => new InRangeValidator(),
                IpValidator::class => new IpValidator(),
                JsonValidator::class => new JsonValidator(),
                NumberValidator::class => new NumberValidator(),
                RegexValidator::class => new RegexValidator(),
                RequiredValidator::class => new RequiredValidator(),
                SubsetValidator::class => new SubsetValidator(),
                UrlValidator::class => new UrlValidator(),
                NestedValidator::class => new NestedValidator(),
            ])
        );
    }
}
