<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
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
            $validator = (new CompareTo($value))
                ->operator($operator);

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
}
