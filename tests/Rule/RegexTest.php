<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use stdClass;
use Yiisoft\Validator\Rule\Regex;
use Yiisoft\Validator\Rule\RegexHandler;
use Yiisoft\Validator\Tests\Rule\Base\DifferentRuleInHandlerTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\RuleTestCase;
use Yiisoft\Validator\Tests\Rule\Base\SerializableRuleTestTrait;
use Yiisoft\Validator\Tests\Rule\Base\SkipOnErrorTestTrait;

final class RegexTest extends RuleTestCase
{
    use DifferentRuleInHandlerTestTrait;
    use SerializableRuleTestTrait;
    use SkipOnErrorTestTrait;

    public function testGetName(): void
    {
        $rule = new Regex('//');
        $this->assertSame('regex', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Regex('//'),
                [
                    'pattern' => '//',
                    'not' => false,
                    'incorrectInputMessage' => [
                        'message' => 'Value should be string.',
                    ],
                    'message' => [
                        'message' => 'Value is invalid.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new Regex('//', not: true),
                [
                    'pattern' => '//',
                    'not' => true,
                    'incorrectInputMessage' => [
                        'message' => 'Value should be string.',
                    ],
                    'message' => [
                        'message' => 'Value is invalid.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    public function dataValidationPassed(): array
    {
        return [
            ['a', [new Regex('/a/')]],
            ['ab', [new Regex('/a/')]],
            ['b', [new Regex('/a/', not: true)]],
        ];
    }

    public function dataValidationFailed(): array
    {
        $incorrectInputMessage = 'Value should be string.';
        $message = 'Value is invalid.';

        return [
            [['a', 'b'], [new Regex('/a/')], ['' => [$incorrectInputMessage]]],
            [['a', 'b'], [new Regex('/a/', not: true)], ['' => [$incorrectInputMessage]]],
            [null, [new Regex('/a/')], ['' => [$incorrectInputMessage]]],
            [null, [new Regex('/a/', not: true)], ['' => [$incorrectInputMessage]]],
            [new stdClass(), [new Regex('/a/')], ['' => [$incorrectInputMessage]]],
            [new stdClass(), [new Regex('/a/', not: true)], ['' => [$incorrectInputMessage]]],
            ['b', [new Regex('/a/')], ['' => [$message]]],

            ['b', [new Regex('/a/', message: 'Custom message.')], ['' => ['Custom message.']]],
            [null, [new Regex('/a/', incorrectInputMessage: 'Custom message.')], ['' => ['Custom message.']]],
        ];
    }

    public function testSkipOnError(): void
    {
        $this->testskipOnErrorInternal(new Regex('//'), new Regex('//', skipOnError: true));
    }

    protected function getDifferentRuleInHandlerItems(): array
    {
        return [Regex::class, RegexHandler::class];
    }
}
