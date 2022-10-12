<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\EachHandler;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Tests\Stub\FakeValidatorFactory;
use Yiisoft\Validator\Tests\Support\RuleWithCustomHandler;
use Yiisoft\Validator\Validator;

final class EachTest extends TestCase
{
    public function testGetName(): void
    {
        $rule = new Each([]);
        $this->assertSame('each', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new Each([
                    new Number(max: 13, integerPattern: '/1/', numberPattern: '/1/'),
                    new Number(max: 14, integerPattern: '/2/', numberPattern: '/2/'),
                ]),
                [
                    'incorrectInputMessage' => [
                        'message' => 'Value must be array or iterable.',
                    ],
                    'message' => [
                        'message' => '{error} {value} given.',
                    ],
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                    'rules' => [
                        [
                            'number',
                            'asInteger' => false,
                            'min' => null,
                            'max' => 13,
                            'notANumberMessage' => [
                                'message' => 'Value must be a number.',
                            ],
                            'tooSmallMessage' => [
                                'message' => 'Value must be no less than {min}.',
                                'parameters' => ['min' => null],
                            ],
                            'tooBigMessage' => [
                                'message' => 'Value must be no greater than {max}.',
                                'parameters' => ['max' => 13],
                            ],
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                            'integerPattern' => '/1/',
                            'numberPattern' => '/1/',
                        ],
                        [
                            'number',
                            'asInteger' => false,
                            'min' => null,
                            'max' => 14,
                            'notANumberMessage' => [
                                'message' => 'Value must be a number.',
                            ],
                            'tooSmallMessage' => [
                                'message' => 'Value must be no less than {min}.',
                                'parameters' => ['min' => null],
                            ],
                            'tooBigMessage' => [
                                'message' => 'Value must be no greater than {max}.',
                                'parameters' => ['max' => 14],
                            ],
                            'skipOnEmpty' => false,
                            'skipOnError' => false,
                            'integerPattern' => '/2/',
                            'numberPattern' => '/2/',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataOptions
     */
    public function testOptions(Each $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();
        $this->assertSame($expectedOptions, $options);
    }

    public function dataValidationPassed(): array
    {
        return [
            [
                [10, 11],
                [new Each([new Number(max: 20)])],
            ],
        ];
    }

    /**
     * @dataProvider dataValidationPassed
     */
    public function testValidationPassed(mixed $data, array $rules): void
    {
        $result = $this->createValidator()->validate($data, $rules);

        $this->assertTrue($result->isValid());
    }

    public function dataValidationFailed(): array
    {
        return [
            [
                [10, 20, 30],
                [new Each([new Number(max: 13)])],
                [
                    '1' => ['Value must be no greater than 13. 20 given.'],
                    '2' => ['Value must be no greater than 13. 30 given.'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataValidationFailed
     */
    public function testValidationFailed(mixed $data, array $rules, array $errorMessagesIndexedByPath): void
    {
        $result = $this->createValidator()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame($errorMessagesIndexedByPath, $result->getErrorMessagesIndexedByPath());
    }

    public function testCustomErrorMessage(): void
    {
        $data = [10, 20, 30];
        $rules = [new Each([new Number(max: 13, tooBigMessage: 'Custom error.')])];

        $result = $this->createValidator()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame(
            [
                '1' => ['Custom error. 20 given.'],
                '2' => ['Custom error. 30 given.'],
            ],
            $result->getErrorMessagesIndexedByPath()
        );
    }

    public function testDifferentRuleInHandler(): void
    {
        $rule = new RuleWithCustomHandler(EachHandler::class);
        $validator = $this->createValidator();

        $this->expectExceptionMessageMatches(
            '/.*' . preg_quote(Each::class) . '.*' . preg_quote(RuleWithCustomHandler::class) . '.*/'
        );
        $validator->validate([], [$rule]);
    }

    private function createValidator(): Validator
    {
        return FakeValidatorFactory::make();
    }
}
