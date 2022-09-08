<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Required;
use Yiisoft\Validator\Rule\RequiredHandler;
use Yiisoft\Validator\RuleHandlerInterface;

final class RequiredHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $rule = new Required();
        $message = 'Value cannot be blank.';

        return [
            [$rule, null, [new Error($message)]],
            [$rule, [], [new Error($message)]],
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
            [new Required(message: 'Custom error'), null, [new Error('Custom error')]],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new RequiredHandler($this->getTranslator());
    }
}
