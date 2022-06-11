<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\GroupRuleHandler;
use Yiisoft\Validator\Rule\RuleHandlerInterface;
use Yiisoft\Validator\Tests\FunctionExists;
use Yiisoft\Validator\Tests\Stub\CustomUrlRule;

final class GroupRuleHandlerTest extends AbstractRuleValidatorTest
{
    protected function setUp(): void
    {
        FunctionExists::$isIdnFunctionExists = true;
        parent::setUp();
    }

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
        return [
            [
                new CustomUrlRule(message: 'Custom error'),
                'domain',
                [new Error('Custom error', [])],
            ],
        ];
    }

    protected function getValidator(): RuleHandlerInterface
    {
        return new GroupRuleHandler();
    }
}
