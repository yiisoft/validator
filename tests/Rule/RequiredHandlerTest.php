<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\RequiredHandler;
use Yiisoft\Validator\Rule\RuleHandlerInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleValidatorTest;

final class RequiredHandlerTest extends AbstractRuleValidatorTest
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

    protected function getValidator(): RuleHandlerInterface
    {
        return new RequiredHandler();
    }
}
