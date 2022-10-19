<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RulesDumper;

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
                            tooSmallMessage: 'Value must be greater than 10.',
                            tooBigMessage: 'Value must be no greater than 100.',
                            skipOnEmpty: true,
                            skipOnError: true
                        ),
                        (fn() => yield from [$rule, $rule])(),
                    ],
                ],
                [
                    'attributeName' => [
                        $dump = [
                            'number',
                            'asInteger' => true,
                            'min' => 10,
                            'max' => 100,
                            'notANumberMessage' => [
                                'message' => 'Value must be an integer.',
                            ],
                            'tooBigMessage' => [
                                'message' => 'Value must be no greater than 100.',
                                'parameters' => ['max' => 100],
                            ],
                            'tooSmallMessage' => [
                                'message' => 'Value must be greater than 10.',
                                'parameters' => ['min' => 10],
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
}
