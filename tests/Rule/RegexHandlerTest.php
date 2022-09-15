<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use stdClass;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\RegexHandler;
use Yiisoft\Validator\RuleHandlerInterface;

final class RegexHandlerTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $rule = new Regex('/a/');
        $ruleNot = new Regex('/a/', not: true);
        $incorrectInputMessage = 'Value should be string.';
        $message = 'Value is invalid.';

        return [
            [$rule, ...$this->createValueAndErrorsPair(['a', 'b'], [new Error($incorrectInputMessage)])],
            [$ruleNot, ...$this->createValueAndErrorsPair(['a', 'b'], [new Error($incorrectInputMessage)])],
            [$rule, ...$this->createValueAndErrorsPair(null, [new Error($incorrectInputMessage)])],
            [$ruleNot, ...$this->createValueAndErrorsPair(null, [new Error($incorrectInputMessage)])],
            [$rule, ...$this->createValueAndErrorsPair(new stdClass(), [new Error($incorrectInputMessage)])],
            [$ruleNot, ...$this->createValueAndErrorsPair(new stdClass(), [new Error($incorrectInputMessage)])],

            [$rule, ...$this->createValueAndErrorsPair('b', [new Error($message)])],
        ];
    }

    public function passedValidationProvider(): array
    {
        $rule = new Regex('/a/');
        $ruleNot = new Regex('/a/', not: true);

        return [
            [$rule, 'a'],
            [$rule, 'ab'],
            [$ruleNot, 'b'],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        return [
            [new Regex('/a/', message: 'Custom message.'), ...$this->createValueAndErrorsPair('b', [new Error('Custom message.')])],
            [new Regex('/a/', incorrectInputMessage: 'Custom message.'), ...$this->createValueAndErrorsPair(null, [new Error('Custom message.')])],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new RegexHandler();
    }
}
