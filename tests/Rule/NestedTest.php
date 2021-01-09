<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\HasLength;
use Yiisoft\Validator\Rule\Nested;
use Yiisoft\Validator\Rule\Number;

/**
 * @group validators
 */
class NestedTest extends TestCase
{
    /**
     * @dataProvider validateDataProvider
     *
     * @param Rule[] $rules
     */
    public function testValidate(array $rules, bool $expectedResult): void
    {
        $values = [
            'author' => [
                'name' => 'Dmitry',
                'age' => 18,
            ],
        ];

        $actualResult = (new Nested($rules))->validate($values);

        $this->assertEquals($expectedResult, $actualResult->isValid());
    }

    public function validateDataProvider()
    {
        return [
            'success' => [
                [
                    'author.name' => [
                        (new HasLength())->min(3),
                    ],
                ],
                true,
            ],
            'error' => [
                [
                    'author.age' => [
                        (new Number())->min(20),
                    ],
                ],
                false,
            ],
            'key not exists' => [
                [
                    'author' => [
                        (new Number())->min(18)->skipOnEmpty(true)->skipOnError(true),
                    ],
                ],
                false,
            ],
        ];
    }
}
