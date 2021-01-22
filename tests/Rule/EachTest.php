<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\ErrorMessage;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rules;
use Yiisoft\Validator\Tests\FormatterMockFactory;

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
            10, 20, 30,
        ];

        $rules = new Rules([
            (new Number())->max(13),
        ]);

        $result = (new Each($rules))->validate($values);
        $errors = $result->getErrors();
        $this->assertFalse($result->isValid());
        $this->assertCount(2, $errors);
        $this->assertEquals([
            'Value must be no greater than 13. 20 given.',
            'Value must be no greater than 13. 30 given.',
        ], $errors);
    }

    /**
     * @test
     */
    public function validateValuesWithTranslator(): void
    {
        $values = [
            10, 20, 30,
        ];

        $rules = new Rules([
            (new Number())->max(13),
        ]);

        $result = (new Each($rules))->validate($values);

        $formatter = (new FormatterMockFactory())->create();
        $errors = $result->getErrors($formatter);

        $this->assertSame('Translate: Translate: Value must be no greater than 13. 20 given.', (string)$errors[0]);
        $this->assertSame('Translate: Translate: Value must be no greater than 13. 30 given.', (string)$errors[1]);
    }

    public function testName(): void
    {
        $this->assertEquals('each', (new Each(new Rules([(new Number())->max(13)])))->getName());
    }

    public function testOptions(): void
    {
        $rules = new Rules([
            (new Number())->max(13),
            (new Number())->max(14),
        ]);

        $this->assertEquals([
            [
                'number',
                'notANumberMessage' => 'Value must be a number.',
                'asInteger' => false,
                'min' => null,
                'tooSmallMessage' => 'Value must be no less than .',
                'max' => 13,
                'tooBigMessage' => 'Value must be no greater than 13.',
                'skipOnEmpty' => false,
                'skipOnError' => true,
            ],
            [
                'number',
                'notANumberMessage' => 'Value must be a number.',
                'asInteger' => false,
                'min' => null,
                'tooSmallMessage' => 'Value must be no less than .',
                'max' => 14,
                'tooBigMessage' => 'Value must be no greater than 14.',
                'skipOnEmpty' => false,
                'skipOnError' => true,
            ],
        ], (new Each($rules))->getOptions());
    }
}
