<?php

declare(strict_types=1);

namespace Yiisoft\Validator\Tests\Rule;

use PHPUnit\Framework\TestCase;
use Yiisoft\Validator\Rule\Each;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\RuleSet;

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
        $values = [10, 20, 30];
        $ruleSet = new RuleSet([new Number(max: 13)]);
        $result = Each::rule($ruleSet)->validate($values);

        $this->assertFalse($result->isValid());
        $this->assertEquals([
            'Value must be no greater than 13. 20 given.',
            'Value must be no greater than 13. 30 given.',
        ], $result->getErrorMessages());
    }

    public function testName(): void
    {
        $this->assertEquals('each', Each::rule(new RuleSet([new Number(max: 13)]))->getName());
    }

    public function testOptions(): void
    {
        $ruleSet = new RuleSet([new Number(max: 13), new Number(max: 14)]);

        $this->assertEquals([
            [
                'number',
                'skipOnEmpty' => false,
                'skipOnError' => false,
                'asInteger' => false,
                'min' => null,
                'max' => 13,
                'notANumberMessage' => 'Value must be a number.',
                'tooSmallMessage' => 'Value must be no less than .',
                'tooBigMessage' => 'Value must be no greater than 13.',
            ],
            [
                'number',
                'skipOnEmpty' => false,
                'skipOnError' => false,
                'asInteger' => false,
                'min' => null,
                'max' => 14,
                'notANumberMessage' => 'Value must be a number.',
                'tooSmallMessage' => 'Value must be no less than .',
                'tooBigMessage' => 'Value must be no greater than 14.',
            ],
        ], Each::rule($ruleSet)->getOptions());
    }
}
