<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\Regex;

use stdClass;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\Regex\Regex;
use Yiisoft\Validator\Rule\Regex\RegexValidator;
use Yiisoft\Validator\Rule\RuleValidatorInterface;
use Yiisoft\Validator\Tests\Rule\AbstractRuleValidatorTest;

/**
 * @group t
 */
final class RegexValidatorTest extends AbstractRuleValidatorTest
{
    private const PATTERN = '/^[a-zA-Z0-9](\.)?([^\/]*)$/m';

    public function failedValidationProvider(): array
    {
        $rule = new Regex('/a/');
        $ruleNot = new Regex('/a/', not: true);

        return [
            [$rule, ['a', 'b'], [new Error($rule->incorrectInputMessage, [])]],
            [$ruleNot, ['a', 'b'], [new Error($rule->incorrectInputMessage, [])]],
            [$rule, null, [new Error($rule->incorrectInputMessage, [])]],
            [$ruleNot, null, [new Error($rule->incorrectInputMessage, [])]],
            [$rule, new stdClass(), [new Error($rule->incorrectInputMessage, [])]],
            [$ruleNot, new stdClass(), [new Error($rule->incorrectInputMessage, [])]],

            [$rule, 'b', [new Error($rule->message, [])]],
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

    protected function getValidator(): RuleValidatorInterface
    {
        return new RegexValidator();
    }

    protected function getConfigClassName(): string
    {
        return Regex::class;
    }
}
