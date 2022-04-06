<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule;
use Yiisoft\Validator\Rule\CompareTo;

class CompareToTest extends TestCase
{
    public function validateWithDefaultArgumentsProvider(): array
    {
        $value = 18449;

        return [
            [$value, $value, true],
            [$value, (string)$value, true],
            [$value, $value + 1, false],
        ];
    }

    /**
     * @dataProvider validateWithDefaultArgumentsProvider
     */
    public function testValidateWithDefaultArguments(int $compareValue, mixed $value, bool $expectedIsValid): void
    {
        $rule = new CompareTo($compareValue);
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function validateWIthOperatorProvider(): array
    {
        $value = 18449;

        return [
            [$value, '===', $value, true],
            [$value, '===', (string)$value, true],
            [$value, '===', (float)$value, true],
            [$value, '===', $value + 1, false],

            [$value, '!=', $value, false],
            [$value, '!=', (string)$value, false],
            [$value, '!=', (float)$value, false],
            [$value, '!=', $value + 0.00001, true],
            [$value, '!=', false, true],

            [$value, '!==', $value, false],
            [$value, '!==', (string)$value, false],
            [$value, '!==', (float)$value, false],
            [$value, '!==', false, true],

            [$value, '>', $value, false],
            [$value, '>', $value + 1, true],
            [$value, '>', $value - 1, false],

            [$value, '>=', $value, true],
            [$value, '>=', $value + 1, true],
            [$value, '>=', $value - 1, false],

            [$value, '<', $value, false],
            [$value, '<', $value + 1, false],
            [$value, '<', $value - 1, true],

            [$value, '<=', $value, true],
            [$value, '<=', $value + 1, false],
            [$value, '<=', $value - 1, true],
        ];
    }

    /**
     * @dataProvider validateWithOperatorProvider
     */
    public function testValidateWithOperator(
        int $compareValue,
        string $operator,
        mixed $value,
        bool $expectedIsValid
    ): void {
        $rule = new CompareTo($compareValue, operator: $operator);
        $result = $rule->validate($value);

        $this->assertSame($expectedIsValid, $result->isValid());
    }

    public function testGetName(): void
    {
        $rule = new CompareTo(1);
        $this->assertEquals('compareTo', $rule->getName());
    }

    public function getOptionsProvider(): array
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
     * @dataProvider getOptionsProvider
     */
    public function testGetOptions(Rule $rule, array $expectedOptions): void
    {
        $this->assertEquals($expectedOptions, $rule->getOptions());
    }
}
