<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use Yiisoft\Validator\ErrorMessage;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rules;
use Yiisoft\Validator\Tests\TranslatorMock;

/**
 * @group validators
 */
class EachTest extends TranslatorMock
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
            new ErrorMessage(
                '{error} {value} given.',
                [
                    'error' => new ErrorMessage('Value must be no greater than {max}.', ['max' => 13]),
                    'value' => 20,
                ]
            ),
            new ErrorMessage(
                '{error} {value} given.',
                [
                    'error' => new ErrorMessage('Value must be no greater than {max}.', ['max' => 13]),
                    'value' => 30,
                ]
            ),
        ], $errors);
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
