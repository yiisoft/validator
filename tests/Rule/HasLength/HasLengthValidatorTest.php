<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule\HasLength;

use stdClass;
use Yiisoft\Validator\Error;
use Yiisoft\Validator\Rule\HasLength\HasLength;
use Yiisoft\Validator\Rule\HasLength\HasLengthValidator;
use Yiisoft\Validator\Tests\Rule\AbstractRuleValidatorTest;

/**
 * @group t
 */
final class HasLengthValidatorTest extends AbstractRuleValidatorTest
{
    public function failedValidationProvider(): array
    {
        $defaultConfig = new HasLength();

        return [
            [$defaultConfig, ['not a string'], [new Error($defaultConfig->message)]],
            [$defaultConfig, new stdClass(), [new Error($defaultConfig->message)]],
            [$defaultConfig, true, [new Error($defaultConfig->message)]],
            [$defaultConfig, false, [new Error($defaultConfig->message)]],

            [new HasLength(max: 25), str_repeat('x', 1250), [new Error($defaultConfig->tooLongMessage, ['max' => 25])]],
            [new HasLength(min: 25, max: 25), str_repeat('x', 125), [new Error($defaultConfig->tooLongMessage, ['max' => 25])]],

            [new HasLength(min: 25, max: 25), '', [new Error($defaultConfig->tooShortMessage, ['min' => 25])]],
            [new HasLength(min: 10, max: 25), str_repeat('x', 5), [new Error($defaultConfig->tooShortMessage, ['min' => 10])]],
            [new HasLength(min: 25), str_repeat('x', 13), [new Error($defaultConfig->tooShortMessage, ['min' => 25])]],
            [new HasLength(min: 25), '', [new Error($defaultConfig->tooShortMessage, ['min' => 25])]],
        ];
    }

    public function passedValidationProvider(): array
    {
        return [
            [new HasLength(), 'Just some string'],

            [new HasLength(min: 25, max: 25), str_repeat('x', 25)],
            [new HasLength(min: 25, max: 25), str_repeat('€', 25)],

            [new HasLength(min: 25), str_repeat('x', 125)],
            [new HasLength(min: 25), str_repeat('€', 25)],

            [new HasLength(max: 25), str_repeat('x', 25)],
            [new HasLength(max: 25), str_repeat('Ä', 24)],
            [new HasLength(max: 25), ''],

            [new HasLength(min: 10, max: 25), str_repeat('x', 15)],
            [new HasLength(min: 10, max: 25), str_repeat('x', 10)],
            [new HasLength(min: 10, max: 25), str_repeat('x', 20)],
            [new HasLength(min: 10, max: 25), str_repeat('x', 25)],

            [new HasLength(min: 1), str_repeat('x', 5)],
            [new HasLength(max: 100), str_repeat('x', 5)],
        ];
    }

    public function customErrorMessagesProvider(): array
    {
        $rule = new HasLength(
            min: 3,
            max: 5,
            message: 'is not string error',
            tooShortMessage: 'is too short test',
            tooLongMessage: 'is too long test'
        );

        return [
            [
                $rule,
                null,
                [new Error('is not string error')],
            ],
            [
                $rule,
                str_repeat('x', 1),
                [new Error('is too short test', ['min' => 3])],
            ],
            [
                $rule,
                str_repeat('x', 6),
                [new Error('is too long test', ['max' => 5])],
            ],
        ];
    }

    /**
     * TODO: add base test case with data provider
     */
    public function testTooShortMessage(): void
    {
        $config = new HasLength(min: 1);
        $result = $this->validate('', $config);

        $this->assertEquals(
            [new Error(
                'This value should contain at least {min, number} {min, plural, one{character} other{characters}}.',
                ['min' => 1],
            )],
            $result->getErrors()
        );
    }

    public function testTooLongMessage(): void
    {
        $config = new HasLength(max: 100);
        $result = $this->validate(str_repeat('x', 1230), $config);

        $this->assertEquals(
            [new Error(
                'This value should contain at most {max, number} {max, plural, one{character} other{characters}}.',
                ['max' => 100],
            )],
            $result->getErrors()
        );
    }

    protected function getValidator(): HasLengthValidator
    {
        return new HasLengthValidator();
    }

    protected function getConfigClassName(): string
    {
        return HasLength::class;
    }
}
