<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rules;

/**
 * @group validators
 */
class EachTest extends TestCase
{
    /**
     * @test
     */
    public function validateValues(): void
    {
        $values = [
            10, 20, 30
        ];

        $rules = new Rules([
            (new Number())->max(13)
        ]);

        $result = (new Each($rules))->validate($values);
        $errors = $result->getErrors();

        $this->assertFalse($result->isValid());
        $this->assertCount(2, $errors);
        $this->assertContains('Value must be no greater than 13. 20 given.', $errors);
        $this->assertContains('Value must be no greater than 13. 30 given.', $errors);
    }

    public function testName(): void
    {
        $this->assertEquals('each', (new Each(new Rules([(new Number())->max(13)])))->getName());
    }

    public function testOptions(): void
    {
        $rules = new Rules([
            (new Number())->max(13),
            (new Number())->max(14)
        ]);

        $this->assertEquals([
            [
                'number',
                'notANumberMessage' => 'Value must be a number.',
                'asInteger' => false,
                'min' => null,
                'tooSmallMessage' => 'Value must be no less than {min}.',
                'max' => 13,
                'tooBigMessage' => 'Value must be no greater than {max}.',
                'skipOnEmpty' => false,
                'skipOnError' => true,
            ],
            [
                'number',
                'notANumberMessage' => 'Value must be a number.',
                'asInteger' => false,
                'min' => null,
                'tooSmallMessage' => 'Value must be no less than {min}.',
                'max' => 14,
                'tooBigMessage' => 'Value must be no greater than {max}.',
                'skipOnEmpty' => false,
                'skipOnError' => true,
            ]
        ], (new Each($rules))->getOptions());
    }
}
