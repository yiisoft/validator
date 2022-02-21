<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RulesDumper;

final class RulesDumperTest extends TestCase
{
    /**
     * @dataProvider asArrayDataProvider
     */
    public function testAsArray($rules, array $expected): void
    {
        $dumper = new RulesDumper(null);
        $result = $dumper->asArray($rules);

        $this->assertEquals($expected, $result);
    }

    public function asArrayDataProvider(): array
    {
        return [
            [
                [
                    'attributeName' => [
                        new Number(
                            asInteger: true,
                            min: 10,
                            max: 100,
                            tooSmallMessage: 'Value must be greater than 10.',
                            tooBigMessage: 'Value must be no greater than 100.',
                            skipOnEmpty: true,
                            skipOnError: true
                        ),
                    ],
                ],
                [
                    'attributeName' => [
                        [
                            'number',
                            'asInteger' => true,
                            'min' => 10,
                            'max' => 100,
                            'notANumberMessage' => 'Value must be an integer.',
                            'tooBigMessage' => 'Value must be no greater than 100.',
                            'tooSmallMessage' => 'Value must be greater than 10.',
                            'skipOnEmpty' => true,
                            'skipOnError' => true,
                        ],
                    ],
                ],
            ],
        ];
    }
}
