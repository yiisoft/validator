<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use RuntimeException;
use Yiisoft\Validator\Rule\LessThanOrEqual;
use Yiisoft\Validator\Tests\Support\ValidatorFactory;
use Yiisoft\Validator\Validator;

final class LessThanOrEqualTest extends TestCase
{
    public function testGetName(): void
    {
        $rule = new LessThanOrEqual(1);
        $this->assertSame('lessThanOrEqual', $rule->getName());
    }

    public function dataOptions(): array
    {
        return [
            [
                new LessThanOrEqual(1),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'message' => [
                        'message' => 'Value must be less than or equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '<=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new LessThanOrEqual(1, type: LessThanOrEqual::TYPE_NUMBER),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'message' => [
                        'message' => 'Value must be less than or equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'number',
                    'operator' => '<=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new LessThanOrEqual(1, type: LessThanOrEqual::TYPE_NUMBER),
                [
                    'targetValue' => 1,
                    'targetAttribute' => null,
                    'message' => [
                        'message' => 'Value must be less than or equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => 1,
                            'targetAttribute' => null,
                            'targetValueOrAttribute' => 1,
                        ],
                    ],
                    'type' => 'number',
                    'operator' => '<=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new LessThanOrEqual(null, 'attribute'),
                [
                    'targetValue' => null,
                    'targetAttribute' => 'attribute',
                    'message' => [
                        'message' => 'Value must be less than or equal to "{targetValueOrAttribute}".',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'attribute',
                            'targetValueOrAttribute' => 'attribute',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '<=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new LessThanOrEqual(targetAttribute: 'test', message: 'Custom message for {targetValueOrAttribute}'),
                [
                    'targetValue' => null,
                    'targetAttribute' => 'test',
                    'message' => [
                        'message' => 'Custom message for {targetValueOrAttribute}',
                        'parameters' => [
                            'targetValue' => null,
                            'targetAttribute' => 'test',
                            'targetValueOrAttribute' => 'test',
                        ],
                    ],
                    'type' => 'string',
                    'operator' => '<=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider dataOptions
     */
    public function testOptions(LessThanOrEqual $rule, array $expectedOptions): void
    {
        $options = $rule->getOptions();
        $this->assertSame($expectedOptions, $options);
    }

    public function testWithoutParameters(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Either "targetValue" or "targetAttribute" must be specified');
        new LessThanOrEqual();
    }

    public function dataValidationPassed(): array
    {
        return [
            [100, [new LessThanOrEqual(101)]],
            [100, [new LessThanOrEqual(100)]],
            ['100', [new LessThanOrEqual('101')]],
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
        $message = 'Value must be less than or equal to "100".';

        return [
            [101, [new LessThanOrEqual(100)], ['' => [$message]]],
            ['101', [new LessThanOrEqual(100)], ['' => [$message]]],
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
        $data = 101;
        $rules = [new LessThanOrEqual(100, message: 'Custom error')];

        $result = $this->createValidator()->validate($data, $rules);

        $this->assertFalse($result->isValid());
        $this->assertSame(
            ['' => ['Custom error']],
            $result->getErrorMessagesIndexedByPath()
        );
    }

    private function createValidator(): Validator
    {
        return ValidatorFactory::make();
    }
}
