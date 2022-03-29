<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\CompareTo;

/**
 * @group validators
 */
class CompareToTest extends TestCase
{
    public function testValidate(): void
    {
        $value = 18449;
        // default config
        $validator = new CompareTo($value);

        $this->assertTrue($validator->validate($value)->isValid());
        $this->assertTrue($validator->validate((string)$value)->isValid());
        $this->assertFalse($validator->validate($value + 1)->isValid());
        foreach ($this->getOperationTestData($value) as $operator => $tests) {
            $validator = new CompareTo($value, operator: $operator);

            foreach ($tests as $test) {
                $this->assertEquals($test[1], $validator->validate($test[0])->isValid(), "Testing $operator");
            }
        }
    }

    protected function getOperationTestData($value): array
    {
        return [
            '===' => [
                [$value, true],
                [(string)$value, true],
                [(float)$value, true],
                [$value + 1, false],
            ],
            '!=' => [
                [$value, false],
                [(string)$value, false],
                [(float)$value, false],
                [$value + 0.00001, true],
                [false, true],
            ],
            '!==' => [
                [$value, false],
                [(string)$value, false],
                [(float)$value, false],
                [false, true],
            ],
            '>' => [
                [$value, false],
                [$value + 1, true],
                [$value - 1, false],
            ],
            '>=' => [
                [$value, true],
                [$value + 1, true],
                [$value - 1, false],
            ],
            '<' => [
                [$value, false],
                [$value + 1, false],
                [$value - 1, true],
            ],
            '<=' => [
                [$value, true],
                [$value + 1, false],
                [$value - 1, true],
            ],
        ];
    }

    public function testName(): void
    {
        $this->assertEquals('compareTo', (new CompareTo(1))->getName());
    }

    public function optionsProvider(): array
    {
        return [
            [
                new CompareTo(1),
                [
                    'compareValue' => 1,
                    'message' => 'Value must be equal to "1".',
                    'type' => 'string',
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new CompareTo(1, type: CompareTo::TYPE_NUMBER),
                [
                    'compareValue' => 1,
                    'message' => 'Value must be equal to "1".',
                    'type' => 'number',
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new CompareTo(1, type: CompareTo::TYPE_NUMBER, operator: '>='),
                [
                    'compareValue' => 1,
                    'message' => 'Value must be greater than or equal to "1".',
                    'type' => 'number',
                    'operator' => '>=',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new CompareTo('YES'),
                [
                    'compareValue' => 'YES',
                    'message' => 'Value must be equal to "YES".',
                    'type' => 'string',
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new CompareTo('YES', skipOnEmpty: true),
                [
                    'compareValue' => 'YES',
                    'message' => 'Value must be equal to "YES".',
                    'type' => 'string',
                    'operator' => '==',
                    'skipOnEmpty' => true,
                    'skipOnError' => false,
                ],
            ],
            [
                new CompareTo('YES', operator: '!=='),
                [
                    'compareValue' => 'YES',
                    'message' => 'Value must not be equal to "YES".',
                    'type' => 'string',
                    'operator' => '!==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
            [
                new CompareTo('YES', message: 'Custom message for {value}'),
                [
                    'compareValue' => 'YES',
                    'message' => 'Custom message for YES',
                    'type' => 'string',
                    'operator' => '==',
                    'skipOnEmpty' => false,
                    'skipOnError' => false,
                ],
            ],
        ];
    }

    /**
     * @dataProvider optionsProvider
     *
     * @param Rule $rule
     * @param array $expected
     */
    public function testOptions(Rule $rule, array $expected): void
    {
        $this->assertEquals($expected, $rule->getOptions());
    }
}
