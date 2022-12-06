<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RulesDumper;
use Yiisoft\Validator\Tests\Support\Data\IteratorWithBooleanKey;
use Yiisoft\Validator\Tests\Support\Rule\RuleWithoutOptions;

final class RulesDumperTest extends TestCase
{
    public function asArrayDataProvider(): array
    {
        return [
            [
                [
                    'attributeName' => [
                        $rule = new Number(
                            asInteger: true,
                            min: 10,
                            max: 100,
                            lessThanMinMessage: 'Value must be greater than 10.',
                            greaterThanMaxMessage: 'Value must be no greater than 100.',
                            skipOnEmpty: true,
                            skipOnError: true
                        ),
                        (fn () => yield from [$rule, $rule])(),
                    ],
                ],
                [
                    'attributeName' => [
                        $dump = [
                            'number',
                            'asInteger' => true,
                            'min' => 10,
                            'max' => 100,
                            'exactly' => null,
                            'incorrectInputMessage' => [
                                'template' => 'The allowed types are integer, float and string.',
                                'parameters' => [],
                            ],
                            'notNumberMessage' => [
                                'template' => 'Value must be an integer.',
                                'parameters' => [],
                            ],
                            'lessThanMinMessage' => [
                                'template' => 'Value must be greater than 10.',
                                'parameters' => ['min' => 10],
                            ],
                            'greaterThanMaxMessage' => [
                                'template' => 'Value must be no greater than 100.',
                                'parameters' => ['max' => 100],
                            ],
                            'notExactlyMessage' => [
                                'template' => 'Value must be equal to {exactly}.',
                                'parameters' => ['exactly' => null],
                            ],
                            'skipOnEmpty' => true,
                            'skipOnError' => true,
                            'integerPattern' => '/^\s*[+-]?\d+\s*$/',
                            'numberPattern' => '/^\s*[-+]?\d*\.?\d+([eE][-+]?\d+)?\s*$/',
                        ],
                        [
                            $dump,
                            $dump,
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider asArrayDataProvider
     */
    public function testAsArray($rules, array $expected): void
    {
        $dumper = new RulesDumper();
        $result = $dumper->asArray($rules);

        $this->assertEquals($expected, $result);
    }

    public function testWrongRuleException(): void
    {
        $dumper = new RulesDumper();

        $this->expectException(InvalidArgumentException::class);

        $message = 'Every rule must implement "Yiisoft\Validator\RuleInterface". Type "string" given.';
        $this->expectExceptionMessage($message);

        $dumper->asArray(['not a rule']);
    }

    public function testWrongKeyException(): void
    {
        $dumper = new RulesDumper();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('An attribute can only have an integer or a string type. bool given.');
        $dumper->asArray(new IteratorWithBooleanKey());
    }

    public function testRuleWithoutOptions(): void
    {
        $dumper = new RulesDumper();
        $rules = [
            new Boolean(),
            new RuleWithoutOptions(),
        ];
        $expectedRules = [
            [
                'boolean',
                'trueValue' => '1',
                'falseValue' => '0',
                'strict' => false,
                'nonScalarMessage' => [
                    'template' => 'Value must be either "{true}" or "{false}".',
                    'parameters' => [
                        'true' => '1',
                        'false' => '0',
                    ],
                ],
                'scalarMessage' => [
                    'template' => 'Value must be either "{true}" or "{false}".',
                    'parameters' => [
                        'true' => '1',
                        'false' => '0',
                    ],
                ],
                'skipOnEmpty' => false,
                'skipOnError' => false,
            ],
            [
                'test',
            ],
        ];

        $this->assertSame($expectedRules, $dumper->asArray($rules));
    }
}
