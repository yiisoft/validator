<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RulesDumper;

final class RulesDumperTest extends TestCase
{
    /**
     * @dataProvider asArrayDataProvider()
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
                        Number::rule()
                            ->integer()
                            ->max(100)
                            ->min(10)
                            ->skipOnError(true)
                            ->skipOnEmpty(true)
                            ->tooSmallMessage('Value must be greater than 10.')
                            ->tooBigMessage('Value must be no greater than 100.'),
                    ],
                ],
                [
                    'attributeName' => [
                        [
                            'number',
                            'asInteger' => true,
                            'max' => 100,
                            'min' => 10,
                            'skipOnError' => true,
                            'skipOnEmpty' => true,
                            'notANumberMessage' => 'Value must be an integer.',
                            'tooBigMessage' => 'Value must be no greater than 100.',
                            'tooSmallMessage' => 'Value must be greater than 10.',
                        ],
                    ],
                ],
            ],
        ];
    }
}
