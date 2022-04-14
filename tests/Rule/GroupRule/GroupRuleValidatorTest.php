<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\GroupRule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\GroupRule\GroupRule;
use Yiisoft\Validator\Rule\GroupRule\GroupRuleValidator;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleValidatorTest;
use Yiisoft\Validator\Tests\Stub\CustomUrlRule;

/**
 * @group t
 */
final class GroupRuleValidatorTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $rule = new CustomUrlRule();

        return [
            [$rule, 'http://доменбольшедвадцатизнаков.рф', [new Error('This value is not a valid.', [])]],
            [$rule, null, [new Error('This value is not a valid.', [])]],
            [$rule, 'domain', [new Error('This value is not a valid.', [])]],
        ];
    }

    public function passedValidationProvider(): array
    {
        $rule = new CustomUrlRule();

        return [
            [$rule, 'http://домен.рф'],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        $rule = new CustomUrlRule(message: 'This value is not valid custom url');

        return [
            [$rule, 'domain', [new Error('This value is not valid custom url', [])]],
        ];
    }

    protected function getValidator(): RuleValidatorInterface
    {
        return new GroupRuleValidator();
    }

    protected function getConfigClassName(): string
    {
        return GroupRule::class;
    }
}
