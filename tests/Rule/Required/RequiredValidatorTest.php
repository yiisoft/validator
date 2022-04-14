<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Required;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Required\Required;
use Yiisoft\Validator\Rule\Required\RequiredValidator;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleValidatorTest;

final class RequiredValidatorTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $rule = new Required();

        return [
            [$rule, null, [new Error($rule->message, [])]],
            [$rule, [], [new Error($rule->message, [])]],
        ];
    }

    public function passedValidationProvider(): array
    {
        $rule = new Required();

        return [
            [$rule, 'not empty'],
            [$rule, ['with', 'elements']],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [new Required(message: 'Custom error'), null, [new Error('Custom error', [])]],
        ];
    }

    protected function getValidator(): RuleValidatorInterface
    {
        return new RequiredValidator();
    }
}
