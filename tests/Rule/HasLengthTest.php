<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Yiisoft\Validator\DataSet\MixedDataSet;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\HasLengthHandler;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithCustomHandler;
use Yiisoft\Validator\Validator;

final class HasLengthTest extends TestCase
{
    public function testGetName(): void
    {
        $rule = new HasLength(min: 3);
        $this->assertSame('hasLength', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new HasLength(min: 3),
                [
                    'min' => 3,
                    'max' => null,
                    'exactly' => null,
                    'lessThanMinMessage' => [
                        'message' => 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.',
                        'parameters' => ['min' => 3],
                    ],
                    'greaterThanMaxMessage' => [
                        'message' => 'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.',
                        'parameters' => ['max' => null],
                    ],
                    'notExactlyMessage' => [
                        'message' => 'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.',
                        'parameters' => ['exactly' => null],
                    ],
                    'message' => [
                        'message' => 'This value must be a string.',
                    ],
                    'encoding' => 'UTF-8',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new HasLength(max: 3),
                [
                    'min' => null,
                    'max' => 3,
                    'exactly' => null,
                    'lessThanMinMessage' => [
                        'message' => 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.',
                        'parameters' => ['min' => null],
                    ],
                    'greaterThanMaxMessage' => [
                        'message' => 'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.',
                        'parameters' => ['max' => 3],
                    ],
                    'notExactlyMessage' => [
                        'message' => 'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.',
                        'parameters' => ['exactly' => null],
                    ],
                    'message' => [
                        'message' => 'This value must be a string.',
                    ],
                    'encoding' => 'UTF-8',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new HasLength(min: 3, max: 4, encoding: 'windows-1251'),
                [
                    'min' => 3,
                    'max' => 4,
                    'exactly' => null,
                    'lessThanMinMessage' => [
                        'message' => 'This value must contain at least {min, number} {min, plural, one{character} other{characters}}.',
                        'parameters' => ['min' => 3],
                    ],
                    'greaterThanMaxMessage' => [
                        'message' => 'This value must contain at most {max, number} {max, plural, one{character} other{characters}}.',
                        'parameters' => ['max' => 4],
                    ],
                    'notExactlyMessage' => [
                        'message' => 'This value must contain exactly {exactly, number} {exactly, plural, one{character} other{characters}}.',
                        'parameters' => ['exactly' => null],
                    ],
                    'message' => [
                        'message' => 'This value must be a string.',
                    ],
                    'encoding' => 'windows-1251',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataOptions
     */
    public function testOptions(HasLength $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();
        $this->assertSame($expectedOptions, $options);
    }

    public function dataValidationPassed(): array
    {
        return [
            [str_repeat('x', 25), [new HasLength(exactly: 25)]],
            [str_repeat('€', 25), [new HasLength(exactly: 25)]],

            [str_repeat('x', 125), [new HasLength(min: 25)]],
            [str_repeat('€', 25), [new HasLength(min: 25)]],

            [str_repeat('x', 25), [new HasLength(max: 25)]],
            [str_repeat('Ä', 24), [new HasLength(max: 25)]],
            ['', [new HasLength(max: 25)]],

            [str_repeat('x', 15), [new HasLength(min: 10, max: 25)]],
            [str_repeat('x', 10), [new HasLength(min: 10, max: 25)]],
            [str_repeat('x', 20), [new HasLength(min: 10, max: 25)]],
            [str_repeat('x', 25), [new HasLength(min: 10, max: 25)]],

            [str_repeat('x', 5), [new HasLength(min: 1)]],
            [str_repeat('x', 5), [new HasLength(max: 100)]],
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
        $message = 'This value must be a string.';
        $greaterThanMaxMessage = 'This value must contain at most 25 characters.';
        $notExactlyMessage = 'This value must contain exactly 25 characters.';
        $lessThanMinMessage = 'This value must contain at least 25 characters.';

        return [
            [['not a string'], [new HasLength(min: 25)], ['' => [$message]]],
            [new MixedDataSet(new stdClass()), [new HasLength(min: 25)], ['' => [$message]]],
            [true, [new HasLength(min: 25)], ['' => [$message]]],
            [false, [new HasLength(min: 25)], ['' => [$message]]],

            [str_repeat('x', 1250), [new HasLength(max: 25)], ['' => [$greaterThanMaxMessage]]],
            [str_repeat('x', 125), [new HasLength(exactly: 25)], ['' => [$notExactlyMessage]]],

            ['', [new HasLength(exactly: 25)], ['' => [$notExactlyMessage]]],
            [
                str_repeat('x', 5),
                [new HasLength(min: 10, max: 25)],
                ['' => ['This value must contain at least 10 characters.']],
            ],
            [str_repeat('x', 13), [new HasLength(min: 25)], ['' => [$lessThanMinMessage]]],
            ['', [new HasLength(min: 25)], ['' => [$lessThanMinMessage]]],
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

    public function dataCustomErrorMessage(): array
    {
        $rules = [
            new HasLength(
                min: 3,
                max: 5,
                message: 'is not string error',
                lessThanMinMessage: 'is too short test',
                greaterThanMaxMessage: 'is too long test'
            ),
        ];

        return [
            [null, $rules, ['' => ['is not string error']]],
            [str_repeat('x', 1), $rules, ['' => ['is too short test']]],
            [str_repeat('x', 6), $rules, ['' => ['is too long test']]],
        ];
    }

    /**
     * @dataProvider dataCustomErrorMessage
     */
    public function testCustomErrorMessage(mixed $data, array $rules, array $errorMessagesIndexedByPath): void
    {
        $result = ValidatorFactory::make()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame($errorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
    }

    public function dataInitWithMinAndMaxAndExactly(): array
    {
        return [
            [['min' => 3, 'exactly' => 3]],
            [['max' => 3, 'exactly' => 3]],
            [['min' => 3, 'max' => 3, 'exactly' => 3]],
        ];
    }

    /**
     * @dataProvider dataInitWithMinAndMaxAndExactly
     */
    public function testInitWithMinAndMaxAndExactly(array $arguments): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$exactly is mutually exclusive with $min and $max.');

        new HasLength(...$arguments);
    }

    public function testInitWithMinAndMax(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Use $exactly instead.');

        new HasLength(min: 3, max: 3);
    }

    public function testInitWithoutRequiredArguments(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('At least one of these attributes must be specified: $min, $max, $exactly.');

        new HasLength();
    }

    public function testDifferentRuleInHandler(): void
    {
        $rule = new RuleWithCustomHandler(HasLengthHandler::class);
        $validator = ValidatorFactory::make();

        $this->expectExceptionMessageMatches(
            '/.*' . preg_quote(HasLength::class) . '.*' . preg_quote(RuleWithCustomHandler::class) . '.*/'
        );
        $validator->validate([], [$rule]);
    }
}
