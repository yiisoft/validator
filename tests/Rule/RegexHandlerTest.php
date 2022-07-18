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
    private const PATTERN = '/^[a-zA-Z0-9](\.)?([^\/]*)$/m';

    public function failedValidationProvider(): array
    {
        $rule = new Regex('/a/');
        $ruleNot = new Regex('/a/', not: true);

        return [
            [$rule, ['a', 'b'], [new Error($rule->getIncorrectInputMessage(), [])]],
            [$ruleNot, ['a', 'b'], [new Error($rule->getIncorrectInputMessage(), [])]],
            [$rule, null, [new Error($rule->getIncorrectInputMessage(), [])]],
            [$ruleNot, null, [new Error($rule->getIncorrectInputMessage(), [])]],
            [$rule, new stdClass(), [new Error($rule->getIncorrectInputMessage(), [])]],
            [$ruleNot, new stdClass(), [new Error($rule->getIncorrectInputMessage(), [])]],

            [$rule, 'b', [new Error($rule->getMessage(), [])]],
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
            [
                new Regex('/a/', message: 'Custom message.'),
                'b',
                [new Error('Custom message.', [])],
            ],
            [
                new Regex('/a/', incorrectInputMessage: 'Custom message.'),
                null,
                [new Error('Custom message.', [])],
            ],
        ];
    }

    protected function getRuleHandler(): RuleHandlerInterface
    {
        return new RegexHandler();
    }
}
