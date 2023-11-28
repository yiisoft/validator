<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Helper;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\BooleanValue;
use Yiisoft\Validator\Rule\Integer;
use Yiisoft\Validator\Helper\RulesDumper;
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
                        $rule = new Integer(
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
                            Integer::class,
                            'min' => 10,
                            'max' => 100,
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
                            'skipOnEmpty' => true,
                            'skipOnError' => true,
                            'pattern' => '/^\s*[+-]?\d+\s*$/',
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
        $result = RulesDumper::asArray($rules);

        $this->assertEquals($expected, $result);
    }

    public function testWrongRuleException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $message = 'Every rule must implement "Yiisoft\Validator\RuleInterface". Type "string" given.';
        $this->expectExceptionMessage($message);

        RulesDumper::asArray(['not a rule']);
    }

    public function testWrongKeyException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('An attribute can only have an integer or a string type. bool given.');
        RulesDumper::asArray(new IteratorWithBooleanKey());
    }

    public function testRuleWithoutOptions(): void
    {
        $rules = [
            new BooleanValue(),
            new RuleWithoutOptions(),
        ];
        $expectedRules = [
            [
                BooleanValue::class,
                'trueValue' => '1',
                'falseValue' => '0',
                'strict' => false,
                'incorrectInputMessage' => [
                    'template' => 'The allowed types are integer, float, string, boolean. {type} given.',
                    'parameters' => [
                        'true' => '1',
                        'false' => '0',
                    ],
                ],
                'message' => [
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

        $this->assertSame($expectedRules, RulesDumper::asArray($rules));
    }
}
