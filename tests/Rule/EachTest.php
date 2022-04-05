<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;

class EachTest extends TestCase
{
    public function validateValues(): void
    {
        $rule = new Each([new Number(max: 13)]);
        $result = $rule->validate([10, 20, 30]);

        $this->assertFalse($result->isValid());
        $this->assertEquals([
            'Value must be no greater than 13. 20 given.',
            'Value must be no greater than 13. 30 given.',
        ], $result->getErrorMessages());
    }

    public function testName(): void
    {
        $rule = new Each([new Number(max: 13)]);
        $this->assertSame('each', $rule->getName());
    }

    public function testOptions(): void
    {
        $rule = new Each([new Number(max: 13), new Number(max: 14)]);
        $expectedOptions = [
            [
                'number',
                'asInteger' => false,
                'min' => null,
                'max' => 13,
                'notANumberMessage' => 'Value must be a number.',
                'tooSmallMessage' => 'Value must be no less than .',
                'tooBigMessage' => 'Value must be no greater than 13.',
                'skipOnEmpty' => false,
                'skipOnError' => false,
            ],
            [
                'number',
                'asInteger' => false,
                'min' => null,
                'max' => 14,
                'notANumberMessage' => 'Value must be a number.',
                'tooSmallMessage' => 'Value must be no less than .',
                'tooBigMessage' => 'Value must be no greater than 14.',
                'skipOnEmpty' => false,
                'skipOnError' => false,
            ],
        ];

        $this->assertEquals($expectedOptions, $rule->getOptions());
    }
}
