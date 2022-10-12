<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\IsTrue;
use Yiisoft\Validator\Rule\IsTrueHandler;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\Tests\Support\RuleWithCustomHandler;
use Yiisoft\Validator\Validator;

final class IsTrueTest extends TestCase
{
    public function testGetName(): void
    {
        $rule = new IsTrue();
        $this->assertSame('isTrue', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new IsTrue(),
                [
                    'trueValue' => '1',
                    'strict' => false,
                    'message' => [
                        'message' => 'The value must be "{true}".',
                        'parameters' => [
                            'true' => '1',
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new IsTrue(trueValue: true, strict: true),
                [
                    'trueValue' => true,
                    'strict' => true,
                    'message' => [
                        'message' => 'The value must be "{true}".',
                        'parameters' => [
                            'true' => 'true',
                        ],
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new IsTrue(
                    trueValue: 'YES',
                    strict: true,
                    message: 'Custom message.',
                    skipOnEmpty: true,
                    skipOnError: true
                ),
                [
                    'trueValue' => 'YES',
                    'strict' => true,
                    'message' => [
                        'message' => 'Custom message.',
                        'parameters' => [
                            'true' => 'YES',
                        ],
                    ],
                    'skipOnEmpty' => true,
                    'skipOnError' => true,
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataOptions
     */
    public function testOptions(IsTrue $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();
        $this->assertSame($expectedOptions, $options);
    }

    public function dataValidationPassed(): array
    {
        return [
            [true, [new IsTrue()]],
            ['1', [new IsTrue()]],
            ['1', [new IsTrue(strict: true)]],
            [true, [new IsTrue(trueValue: true, strict: true)]],
        ];
    }

    /**
     * @dataProvider dataValidationPassed
     */
    public function testValidationPassed(mixed $data, array $rules): void
    {
        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertTrue($result->isValid());
    }

    public function dataValidationFailed(): array
    {
        return [
            ['5', [new IsTrue()], ['' => ['The value must be "1".']]],
            [null, [new IsTrue()], ['' => ['The value must be "1".']]],
            [[], [new IsTrue()], ['' => ['The value must be "1".']]],
            [true, [new IsTrue(strict: true)], ['' => ['The value must be "1".']]],
            ['1', [new IsTrue(trueValue: true, strict: true)], ['' => ['The value must be "true".']]],
            [[], [new IsTrue(trueValue: true, strict: true)], ['' => ['The value must be "true".']]],

            [false, [new IsTrue()], ['' => ['The value must be "1".']]],
            ['0', [new IsTrue()], ['' => ['The value must be "1".']]],
            ['0', [new IsTrue(strict: true)], ['' => ['The value must be "1".']]],
            [false, [new IsTrue(trueValue: true, strict: true)], ['' => ['The value must be "true".']]],
        ];
    }

    /**
     * @dataProvider dataValidationFailed
     */
    public function testValidationFailed(mixed $data, array $rules, array $errorMessagesIndexedByPath): void
    {
        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame($errorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
    }

    public function testCustomErrorMessage(): void
    {
        $data = 5;
        $rules = [new IsTrue(message: 'Custom error.')];

        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame(
            ['' => ['Custom error.']],
            $result->getErrorMessagesIndexedByPath()
        );
    }

    public function testDifferentRuleInHandler(): void
    {
        $rule = new RuleWithCustomHandler(IsTrueHandler::class);
        $validator = ValidatorFactory::make();

        $this->expectExceptionMessageMatches(
            '/.*' . preg_quote(IsTrue::class) . '.*' . preg_quote(RuleWithCustomHandler::class) . '.*/'
        );
        $validator->validate([], [$rule]);
    }
}
